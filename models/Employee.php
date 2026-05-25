<?php
class Employee
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($search = '')
    {
        $sql = "SELECT * FROM users WHERE role = 'empleado'";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND name LIKE ?";
            $params[] = "%{$search}%";
        }
        $sql .= " ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND role = 'empleado'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getProduction($userId, $limit = 200)
    {
        $sql = "SELECT ep.*, p.name as product_name FROM employee_production ep
                JOIN products p ON p.id = ep.product_id
                WHERE ep.user_id = ?
                ORDER BY ep.produced_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function addProduction($data)
    {
        $bonusPer10 = $this->getBonusPer10Units($data['user_id']);
        $accumulated = $this->getAccumulatedUnits($data['user_id']);
        $newTotal = $accumulated + $data['quantity'];
        $milestonesBefore = intdiv($accumulated, 10);
        $milestonesAfter = intdiv($newTotal, 10);
        $bonusEarned = ($milestonesAfter - $milestonesBefore) * $bonusPer10;

        $this->db->getConnection()->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO employee_production (user_id, product_id, quantity, bonus_earned, produced_at, notes) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['user_id'],
                $data['product_id'],
                $data['quantity'],
                $bonusEarned,
                $data['produced_at'],
                $data['notes'] ?? null,
            ]);

            $recipeItems = $this->getProductRecipe($data['product_id']);
            foreach ($recipeItems as $recipe) {
                $deductQty = $recipe['quantity'] * $data['quantity'];
                $rmStmt = $this->db->prepare("UPDATE raw_materials SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $result = $rmStmt->execute([$deductQty, $recipe['raw_material_id'], $deductQty]);
                if ($rmStmt->rowCount() === 0) {
                    throw new Exception("Stock insuficiente de: " . $recipe['material_name']);
                }
            }

            if ($bonusEarned > 0) {
                $stmt2 = $this->db->prepare(
                    "INSERT INTO employee_payments (user_id, payment_type, amount_usd, units_produced, period_start, period_end, notes)
                     VALUES (?, 'bono', ?, ?, ?, ?, ?)"
                );
                $stmt2->execute([
                    $data['user_id'],
                    $bonusEarned,
                    $data['quantity'],
                    $data['produced_at'],
                    $data['produced_at'],
                    "Bono por cada 10 unidades producidas",
                ]);
            }

            $this->db->getConnection()->commit();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }

    private function getProductRecipe($productId)
    {
        $stmt = $this->db->prepare(
            "SELECT ri.*, rm.name as material_name FROM recipe_items ri
             JOIN raw_materials rm ON rm.id = ri.raw_material_id
             WHERE ri.product_id = ?"
        );
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    private function getBonusPer10Units($userId)
    {
        $stmt = $this->db->prepare("SELECT bonus_per_10_units FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? (float) $row['bonus_per_10_units'] : 0;
    }

    public function getAccumulatedUnits($userId)
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(quantity),0) FROM employee_production WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getPayments($userId = null, $limit = 200)
    {
        $sql = "SELECT ep.*, u.name as employee_name FROM employee_payments ep
                JOIN users u ON u.id = ep.user_id WHERE 1=1";
        $params = [];
        if ($userId) {
            $sql .= " AND ep.user_id = ?";
            $params[] = $userId;
        }
        $sql .= " ORDER BY ep.paid_at DESC LIMIT ?";
        $params[] = $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function addPayment($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO employee_payments (user_id, payment_type, amount_usd, units_produced, period_start, period_end, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['user_id'],
            $data['payment_type'],
            $data['amount_usd'],
            $data['units_produced'] ?? 0,
            $data['period_start'],
            $data['period_end'],
            $data['notes'] ?? null,
        ]);
    }

    public function getProductionStats($userId, $period = 'all')
    {
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(quantity),0) as total_qty, COALESCE(SUM(bonus_earned),0) as total_bonus
                FROM employee_production WHERE user_id = ?";
        $params = [$userId];
        $now = new DateTime();

        switch ($period) {
            case 'today':
                $sql .= " AND produced_at >= ? AND produced_at <= ?";
                $params[] = $now->format('Y-m-d');
                $params[] = $now->format('Y-m-d');
                break;
            case 'week':
                $monday = (clone $now)->modify('monday this week');
                $sunday = (clone $monday)->modify('+6 days');
                $sql .= " AND produced_at >= ? AND produced_at <= ?";
                $params[] = $monday->format('Y-m-d');
                $params[] = $sunday->format('Y-m-d');
                break;
            case 'month':
                $first = (clone $now)->modify('first day of this month');
                $last = (clone $now)->modify('last day of this month');
                $sql .= " AND produced_at >= ? AND produced_at <= ?";
                $params[] = $first->format('Y-m-d');
                $params[] = $last->format('Y-m-d');
                break;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getProductionReport($period = 'all')
    {
        $sql = "SELECT u.id, u.name,
                       COUNT(ep.id) as production_count,
                       COALESCE(SUM(ep.quantity),0) as total_qty,
                       COALESCE(SUM(ep.bonus_earned),0) as total_bonus
                FROM users u
                LEFT JOIN employee_production ep ON ep.user_id = u.id";
        $params = [];
        $now = new DateTime();

        switch ($period) {
            case 'today':
                $sql .= " AND ep.produced_at >= ? AND ep.produced_at <= ?";
                $params[] = $now->format('Y-m-d');
                $params[] = $now->format('Y-m-d');
                break;
            case 'week':
                $monday = (clone $now)->modify('monday this week');
                $sunday = (clone $monday)->modify('+6 days');
                $sql .= " AND ep.produced_at >= ? AND ep.produced_at <= ?";
                $params[] = $monday->format('Y-m-d');
                $params[] = $sunday->format('Y-m-d');
                break;
            case 'month':
                $first = (clone $now)->modify('first day of this month');
                $last = (clone $now)->modify('last day of this month');
                $sql .= " AND ep.produced_at >= ? AND ep.produced_at <= ?";
                $params[] = $first->format('Y-m-d');
                $params[] = $last->format('Y-m-d');
                break;
        }

        $sql .= " WHERE u.role = 'empleado' GROUP BY u.id ORDER BY total_qty DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
