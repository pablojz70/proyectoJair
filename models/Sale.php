<?php
class Sale
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($userId = null, $filters = [])
    {
        $sql = "SELECT s.*, c.full_name as client_name, e.name as employee_name FROM sales s JOIN clients c ON c.id = s.client_id LEFT JOIN users e ON e.id = s.employee_id WHERE 1=1";
        $params = [];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(s.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(s.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['type'])) {
            $sql .= " AND s.sale_type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['client_id'])) {
            $sql .= " AND s.client_id = ?";
            $params[] = $filters['client_id'];
        }

        $sql .= " ORDER BY s.created_at DESC LIMIT 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT s.*, c.full_name as client_name, e.name as employee_name FROM sales s JOIN clients c ON c.id = s.client_id LEFT JOIN users e ON e.id = s.employee_id WHERE s.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getItems($saleId)
    {
        $sql = "SELECT si.*, p.name as product_name, p.type as product_type
                FROM sale_items si
                JOIN products p ON p.id = si.product_id
                WHERE si.sale_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO sales (user_id, client_id, employee_id, sale_type, total_usd, total_bs, exchange_rate, status, due_date)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['user_id'],
                $data['client_id'],
                $data['employee_id'] ?? null,
                $data['sale_type'],
                $data['total_usd'],
                $data['total_bs'],
                $data['exchange_rate'],
                $data['status'],
                $data['due_date'] ?? null,
            ]);
            $saleId = $this->db->lastInsertId();

            foreach ($data['items'] as $item) {
                $stmt = $this->db->prepare(
                    "INSERT INTO sale_items (sale_id, product_id, quantity, unit_price_usd, subtotal_usd) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $saleId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['unit_price_usd'],
                    $item['subtotal_usd'],
                ]);

                if ($item['product_type'] === 'simple') {
                    $pStmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                    $pStmt->execute([$item['quantity'], $item['product_id']]);
                } else {
                    $pYield = $this->db->prepare("SELECT recipe_yield FROM products WHERE id = ?");
                    $pYield->execute([$item['product_id']]);
                    $yield = (int) ($pYield->fetchColumn() ?: 1);
                    $recipeItems = $this->getProductRecipe($item['product_id']);
                    foreach ($recipeItems as $recipe) {
                        $deductQty = ($recipe['quantity'] / $yield) * $item['quantity'];
                        $rmStmt = $this->db->prepare("UPDATE raw_materials SET stock = stock - ? WHERE id = ? AND stock >= ?");
                        $result = $rmStmt->execute([$deductQty, $recipe['raw_material_id'], $deductQty]);
                        if ($rmStmt->rowCount() === 0) {
                            throw new Exception("Stock insuficiente de: " . $recipe['material_name']);
                        }
                    }
                }
            }

            $this->db->getConnection()->commit();
            return $saleId;
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

    public function getTotalsByPeriod($userId = null, $period = 'today')
    {
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(total_usd),0) as total_usd, COALESCE(SUM(total_bs),0) as total_bs FROM sales WHERE ";
        $params = [];
        $now = new DateTime();

        switch ($period) {
            case 'today':
                $sql .= "created_at >= ? AND created_at < ?";
                $params[] = $now->format('Y-m-d 00:00:00');
                $params[] = $now->format('Y-m-d 23:59:59');
                break;
            case 'week':
                $monday = (clone $now)->modify('monday this week')->setTime(0, 0, 0);
                $nextMonday = (clone $monday)->modify('+7 days');
                $sql .= "created_at >= ? AND created_at < ?";
                $params[] = $monday->format('Y-m-d H:i:s');
                $params[] = $nextMonday->format('Y-m-d H:i:s');
                break;
            case 'month':
                $firstDay = (clone $now)->modify('first day of this month')->setTime(0, 0, 0);
                $nextMonth = (clone $firstDay)->modify('+1 month');
                $sql .= "created_at >= ? AND created_at < ?";
                $params[] = $firstDay->format('Y-m-d H:i:s');
                $params[] = $nextMonth->format('Y-m-d H:i:s');
                break;
            default:
                $sql .= "1=1";
        }

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getByType($userId = null)
    {
        $sql = "SELECT sale_type, COUNT(*) as count, COALESCE(SUM(total_usd),0) as total FROM sales";
        $params = [];

        $conditions = [];
        if ($userId && !Session::isAdmin()) {
            $conditions[] = "user_id = ?";
            $params[] = $userId;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY sale_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTopClients($userId = null, $limit = 5)
    {
        $sql = "SELECT c.id, c.full_name, COUNT(s.id) as sale_count, COALESCE(SUM(s.total_usd),0) as total
                FROM sales s JOIN clients c ON c.id = s.client_id";
        $params = [];

        $conditions = [];
        if ($userId && !Session::isAdmin()) {
            $conditions[] = "s.user_id = ?";
            $params[] = $userId;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY c.id ORDER BY total DESC LIMIT " . (int) $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTotalPayments($saleId)
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount_usd),0) as total FROM payments WHERE sale_id = ?");
        $stmt->execute([$saleId]);
        return $stmt->fetchColumn();
    }

    public function delete($id)
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $items = $this->getItems($id);
            foreach ($items as $item) {
                if ($item['product_type'] === 'simple') {
                    $stmt = $this->db->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                } else {
                    $pYield = $this->db->prepare("SELECT recipe_yield FROM products WHERE id = ?");
                    $pYield->execute([$item['product_id']]);
                    $yield = (int) ($pYield->fetchColumn() ?: 1);
                    $recipeItems = $this->getProductRecipe($item['product_id']);
                    foreach ($recipeItems as $recipe) {
                        $restoreQty = ($recipe['quantity'] / $yield) * $item['quantity'];
                        $stmt = $this->db->prepare("UPDATE raw_materials SET stock = stock + ? WHERE id = ?");
                        $stmt->execute([$restoreQty, $recipe['raw_material_id']]);
                    }
                }
            }

            $stmt = $this->db->prepare("DELETE FROM payments WHERE sale_id = ?");
            $stmt->execute([$id]);

            $stmt = $this->db->prepare("DELETE FROM sale_items WHERE sale_id = ?");
            $stmt->execute([$id]);

            $stmt = $this->db->prepare("DELETE FROM sales WHERE id = ?");
            $stmt->execute([$id]);

            $this->db->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }
}
