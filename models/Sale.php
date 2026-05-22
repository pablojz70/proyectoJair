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
        $sql = "SELECT s.*, c.full_name as client_name, e.name as employee_name FROM sales s JOIN clients c ON c.id = s.client_id LEFT JOIN employees e ON e.id = s.employee_id WHERE 1=1";
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
        $stmt = $this->db->prepare("SELECT s.*, c.full_name as client_name, e.name as employee_name FROM sales s JOIN clients c ON c.id = s.client_id LEFT JOIN employees e ON e.id = s.employee_id WHERE s.id = ?");
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
                    $recipeItems = $this->getProductRecipe($item['product_id']);
                    foreach ($recipeItems as $recipe) {
                        $deductQty = $recipe['quantity'] * $item['quantity'];
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

        switch ($period) {
            case 'today':
                $sql .= "DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $sql .= "YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $sql .= "MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
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
}
