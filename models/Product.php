<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($userId = null, $type = null, $search = '')
    {
        $sql = "SELECT p.* FROM products p WHERE 1=1";
        $params = [];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND p.user_id = ?";
            $params[] = $userId;
        }

        if ($type) {
            $sql .= " AND p.type = ?";
            $params[] = $type;
        }

        if (!empty($search)) {
            $sql .= " AND p.name LIKE ?";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO products (user_id, name, description, type, stock, sale_price_usd, production_cost_usd) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['description'] ?? null,
            $data['type'],
            $data['type'] === 'simple' ? ($data['stock'] ?? 0) : null,
            $data['sale_price_usd'],
            $data['production_cost_usd'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE products SET name = ?, description = ?, sale_price_usd = ?";
        $params = [$data['name'], $data['description'] ?? null, $data['sale_price_usd']];

        if ($data['type'] === 'simple') {
            $sql .= ", stock = ?";
            $params[] = $data['stock'] ?? 0;
        }

        $sql .= ", production_cost_usd = ? WHERE id = ?";
        $params[] = $data['production_cost_usd'] ?? null;
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deductStock($id, $quantity)
    {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        return $stmt->execute([$quantity, $id, $quantity]);
    }

    public function getRecipe($productId)
    {
        $sql = "SELECT ri.*, rm.name as material_name, rm.unit, rm.unit_cost_usd, rm.stock as material_stock
                FROM recipe_items ri
                JOIN raw_materials rm ON rm.id = ri.raw_material_id
                WHERE ri.product_id = ?
                ORDER BY rm.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function addRecipeItem($productId, $rawMaterialId, $quantity)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO recipe_items (product_id, raw_material_id, quantity) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$productId, $rawMaterialId, $quantity]);
    }

    public function removeRecipeItem($id)
    {
        $stmt = $this->db->prepare("DELETE FROM recipe_items WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function calculateProductionCost($productId)
    {
        $sql = "SELECT SUM(ri.quantity * rm.unit_cost_usd) as total_cost
                FROM recipe_items ri
                JOIN raw_materials rm ON rm.id = ri.raw_material_id
                WHERE ri.product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        $result = $stmt->fetch();
        return $result['total_cost'] ?? 0;
    }

    public function updateProductionCost($productId)
    {
        $cost = $this->calculateProductionCost($productId);
        $stmt = $this->db->prepare("UPDATE products SET production_cost_usd = ? WHERE id = ?");
        return $stmt->execute([$cost, $productId]);
    }

    public function checkRecipeStock($productId, $quantity = 1)
    {
        $recipe = $this->getRecipe($productId);
        $missing = [];

        foreach ($recipe as $item) {
            $needed = $item['quantity'] * $quantity;
            if ($item['material_stock'] < $needed) {
                $missing[] = [
                    'name' => $item['material_name'],
                    'available' => $item['material_stock'],
                    'needed' => $needed,
                    'unit' => $item['unit'],
                ];
            }
        }

        return $missing;
    }

    public function getAvailableProducts($userId = null)
    {
        $sql = "SELECT p.* FROM products p WHERE ";
        $params = [];

        $conditions = ["(p.type = 'simple' AND (p.stock IS NULL OR p.stock > 0))"];
        $conditions[] = "p.type = 'compuesto'";

        $sql .= "(" . implode(" OR ", $conditions) . ")";

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND p.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY p.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
