<?php
class Payment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getBySale($saleId)
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE sale_id = ? ORDER BY created_at DESC");
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }

    public function getByClient($clientId, $userId = null)
    {
        $sql = "SELECT p.*, s.total_usd, s.total_bs, s.created_at as sale_date, c.full_name as client_name
                FROM payments p
                JOIN sales s ON s.id = p.sale_id
                JOIN clients c ON c.id = s.client_id
                WHERE s.client_id = ?";
        $params = [$clientId];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $this->db->getConnection()->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO payments (sale_id, amount_usd, amount_bs, exchange_rate, notes) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $data['sale_id'],
                $data['amount_usd'],
                $data['amount_bs'],
                $data['exchange_rate'],
                $data['notes'] ?? null,
            ]);

            $totalPaid = $this->getTotalPaid($data['sale_id']);
            $saleStmt = $this->db->prepare("SELECT total_usd FROM sales WHERE id = ?");
            $saleStmt->execute([$data['sale_id']]);
            $sale = $saleStmt->fetch();

            if ($totalPaid >= $sale['total_usd']) {
                $upStmt = $this->db->prepare("UPDATE sales SET status = 'pagada' WHERE id = ?");
                $upStmt->execute([$data['sale_id']]);
            }

            $this->db->getConnection()->commit();
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            throw $e;
        }
    }

    public function getTotalPaid($saleId)
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount_usd),0) FROM payments WHERE sale_id = ?");
        $stmt->execute([$saleId]);
        return (float) $stmt->fetchColumn();
    }

    public function getDebts($userId = null)
    {
        $sql = "SELECT s.id as sale_id, s.total_usd, s.total_bs, s.due_date, s.created_at as sale_date,
                       c.id as client_id, c.full_name as client_name, c.phone as client_phone,
                       COALESCE((SELECT SUM(amount_usd) FROM payments WHERE sale_id = s.id), 0) as paid
                FROM sales s
                JOIN clients c ON c.id = s.client_id
                WHERE s.sale_type = 'credito' AND s.status != 'pagada'";
        $params = [];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY s.due_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getDebtsByClient($clientId, $userId = null)
    {
        $sql = "SELECT s.id as sale_id, s.total_usd, s.total_bs, s.due_date, s.created_at as sale_date, c.phone as client_phone,
                       COALESCE((SELECT SUM(amount_usd) FROM payments WHERE sale_id = s.id), 0) as paid
                FROM sales s JOIN clients c ON c.id = s.client_id
                WHERE s.client_id = ? AND s.sale_type = 'credito' AND s.status != 'pagada'";
        $params = [$clientId];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY s.due_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPaymentStats($userId = null, $period = null)
    {
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount_usd),0) as total_usd, COALESCE(SUM(amount_bs),0) as total_bs
                FROM payments p JOIN sales s ON s.id = p.sale_id WHERE 1=1";
        $params = [];

        if ($userId && !Session::isAdmin()) {
            $sql .= " AND s.user_id = ?";
            $params[] = $userId;
        }

        if ($period === 'month') {
            $now = new DateTime();
            $first = (clone $now)->modify('first day of this month')->setTime(0, 0, 0);
            $nextMonth = (clone $first)->modify('+1 month');
            $sql .= " AND p.created_at >= ? AND p.created_at < ?";
            $params[] = $first->format('Y-m-d H:i:s');
            $params[] = $nextMonth->format('Y-m-d H:i:s');
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getOverdueClients($userId = null)
    {
        $debts = $this->getDebts($userId);
        $clients = [];

        foreach ($debts as $debt) {
            $debtAmount = $debt['total_usd'] - $debt['paid'];
            if ($debtAmount <= 0) continue;

            if (!isset($clients[$debt['client_id']])) {
                $clients[$debt['client_id']] = [
                    'client_id' => $debt['client_id'],
                    'client_name' => $debt['client_name'],
                    'client_phone' => $debt['client_phone'] ?? '',
                    'total_debt' => 0,
                    'count' => 0,
                    'overdue' => false,
                ];
            }

            $clients[$debt['client_id']]['total_debt'] += $debtAmount;
            $clients[$debt['client_id']]['count']++;

            if ($debt['due_date'] && strtotime($debt['due_date']) < time()) {
                $clients[$debt['client_id']]['overdue'] = true;
            }
        }

        return $clients;
    }
}
