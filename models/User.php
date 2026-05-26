<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, username, email, password, phone, role) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT),
            $data['phone'] ?? null,
            $data['role'] ?? 'vendedor',
        ]);
        return $this->db->lastInsertId();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getVendors()
    {
        $stmt = $this->db->query("SELECT * FROM users WHERE role = 'vendedor' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE users SET name = ?, username = ?, email = ?, phone = ?, role = ?,
                employee_status = ?";
        $params = [
            $data['name'], $data['username'], $data['email'], $data['phone'] ?? null,
            $data['role'] ?? 'vendedor',
            $data['employee_status'] ?? 'activo',
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function authenticate($login, $password)
    {
        $user = $this->findByEmail($login);
        if (!$user) {
            $user = $this->findByUsername($login);
        }
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function existsByUsername($username, $excludeId = null)
    {
        $sql = "SELECT id FROM users WHERE username = ?";
        $params = [$username];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ? true : false;
    }
}
