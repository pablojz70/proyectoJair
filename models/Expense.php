<?php
class Expense
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($periodStart = null, $periodEnd = null, $type = null)
    {
        $sql = "SELECT * FROM expenses WHERE 1=1";
        $params = [];

        if ($periodStart) {
            $sql .= " AND expense_date >= ?";
            $params[] = $periodStart;
        }
        if ($periodEnd) {
            $sql .= " AND expense_date <= ?";
            $params[] = $periodEnd;
        }
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }

        $sql .= " ORDER BY expense_date DESC, created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM expenses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO expenses (type, description, amount_usd, expense_date, notes) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['type'],
            $data['description'],
            $data['amount_usd'],
            $data['expense_date'],
            $data['notes'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE expenses SET type=?, description=?, amount_usd=?, expense_date=?, notes=? WHERE id=?"
        );
        return $stmt->execute([
            $data['type'],
            $data['description'],
            $data['amount_usd'],
            $data['expense_date'],
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalsByPeriod($periodStart, $periodEnd)
    {
        $sql = "SELECT type, COALESCE(SUM(amount_usd),0) as total FROM expenses
                WHERE expense_date BETWEEN ? AND ? GROUP BY type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periodStart, $periodEnd]);
        $result = ['materia_prima' => 0, 'empleado' => 0, 'otro' => 0, 'total' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['type']] = (float) $row['total'];
            $result['total'] += (float) $row['total'];
        }
        return $result;
    }
}
