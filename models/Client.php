<?php
class Client
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($userId = null, $search = '')
    {
        $sql = "SELECT * FROM clients WHERE 1=1";
        $params = [];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        if (!empty($search)) {
            $sql .= " AND (full_name LIKE ? OR cedula_rif LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY full_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id, $userId = null)
    {
        $sql = "SELECT * FROM clients WHERE id = ?";
        $params = [$id];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO clients (user_id, full_name, cedula_rif, phone, notes) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['full_name'],
            $data['cedula_rif'],
            $data['phone'] ?? null,
            $data['notes'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE clients SET full_name = ?, cedula_rif = ?, phone = ?, notes = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['full_name'],
            $data['cedula_rif'],
            $data['phone'] ?? null,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete($id, $userId = null)
    {
        $sql = "DELETE FROM clients WHERE id = ?";
        $params = [$id];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function existsByCedula($cedula, $userId, $excludeId = null)
    {
        $sql = "SELECT id FROM clients WHERE cedula_rif = ? AND user_id = ?";
        $params = [$cedula, $userId];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ? true : false;
    }

    public function getSalesHistory($clientId, $userId = null)
    {
        $sql = "SELECT s.* FROM sales s WHERE s.client_id = ?";
        $params = [$clientId];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY s.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
