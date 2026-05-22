<?php
class RawMaterial
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($userId = null, $search = '')
    {
        $sql = "SELECT * FROM raw_materials WHERE 1=1";
        $params = [];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

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
        $stmt = $this->db->prepare("SELECT * FROM raw_materials WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO raw_materials (user_id, name, unit, stock, unit_cost_usd, min_stock) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['name'],
            $data['unit'],
            $data['stock'] ?? 0,
            $data['unit_cost_usd'] ?? 0,
            $data['min_stock'] ?? 5,
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE raw_materials SET name = ?, unit = ?, stock = ?, unit_cost_usd = ?, min_stock = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['name'],
            $data['unit'],
            $data['stock'] ?? 0,
            $data['unit_cost_usd'] ?? 0,
            $data['min_stock'] ?? 5,
            $id,
        ]);
    }

    public function adjustStock($id, $quantity)
    {
        $stmt = $this->db->prepare("UPDATE raw_materials SET stock = stock + ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }

    public function deductStock($id, $quantity)
    {
        $stmt = $this->db->prepare("UPDATE raw_materials SET stock = stock - ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM raw_materials WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getLowStock($userId = null)
    {
        $sql = "SELECT * FROM raw_materials WHERE stock <= min_stock";
        $params = [];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUnits()
    {
        return ['kg', 'litros', 'unidades', 'gramos', 'ml', 'lb', 'oz', 'm', 'cm', 'cajas', 'paquetes', 'docenas', 'otros'];
    }
}
