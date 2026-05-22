<?php
class FinancePeriod
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM finance_periods ORDER BY period_start DESC LIMIT 50");
        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM finance_periods WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByRange($start, $end)
    {
        $stmt = $this->db->prepare("SELECT * FROM finance_periods WHERE period_start = ? AND period_end = ?");
        $stmt->execute([$start, $end]);
        return $stmt->fetch();
    }

    public function getCurrentOrCreate($periodStart, $periodEnd)
    {
        $existing = $this->findByRange($periodStart, $periodEnd);
        if ($existing) return $existing;

        $sales = $this->getSalesInRange($periodStart, $periodEnd);
        $expenses = $this->getExpensesInRange($periodStart, $periodEnd);

        $totalSales = $sales['total'];
        $totalExpenses = $expenses['total'];
        $grossProfit = $totalSales - $totalExpenses;
        $commission10 = $grossProfit * 0.10;
        $netProfit = $grossProfit - $commission10;

        $stmt = $this->db->prepare(
            "INSERT INTO finance_periods (period_start, period_end, total_sales_usd, total_expenses_usd, gross_profit_usd, commission_10pct_usd, net_profit_usd)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$periodStart, $periodEnd, $totalSales, $totalExpenses, $grossProfit, $commission10, $netProfit]);
        return $this->findById($this->db->lastInsertId());
    }

    public function updateAllocations($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE finance_periods SET savings_usd=?, dividends_usd=?, other_allocations_usd=?, notes=? WHERE id=?"
        );
        return $stmt->execute([
            $data['savings_usd'] ?? 0,
            $data['dividends_usd'] ?? 0,
            $data['other_allocations_usd'] ?? 0,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function recalculate($id)
    {
        $period = $this->findById($id);
        if (!$period) return false;

        $sales = $this->getSalesInRange($period['period_start'], $period['period_end']);
        $expenses = $this->getExpensesInRange($period['period_start'], $period['period_end']);

        $totalSales = $sales['total'];
        $totalExpenses = $expenses['total'];
        $grossProfit = $totalSales - $totalExpenses;
        $commission10 = $grossProfit * 0.10;
        $netProfit = $grossProfit - $commission10;

        $stmt = $this->db->prepare(
            "UPDATE finance_periods SET total_sales_usd=?, total_expenses_usd=?, gross_profit_usd=?, commission_10pct_usd=?, net_profit_usd=? WHERE id=?"
        );
        return $stmt->execute([$totalSales, $totalExpenses, $grossProfit, $commission10, $netProfit, $id]);
    }

    private function getSalesInRange($start, $end)
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total_usd),0) as total, COUNT(*) as count FROM sales WHERE DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$start, $end]);
        return $stmt->fetch();
    }

    private function getExpensesInRange($start, $end)
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount_usd),0) as total, COUNT(*) as count FROM expenses WHERE expense_date BETWEEN ? AND ?");
        $stmt->execute([$start, $end]);
        $result = $stmt->fetch();
        $result['total'] = (float) $result['total'];
        $stmt2 = $this->db->prepare("SELECT COALESCE(SUM(amount_usd),0) as total FROM employee_payments WHERE DATE(paid_at) BETWEEN ? AND ?");
        $stmt2->execute([$start, $end]);
        $result['total'] += (float) $stmt2->fetchColumn();
        return $result;
    }

    public function getSalesInRangeRaw($start, $end)
    {
        return $this->getSalesInRange($start, $end);
    }

    public function getExpensesInRangeRaw($start, $end)
    {
        return $this->getExpensesInRange($start, $end);
    }

    public function getEmployeePaymentsInRange($start, $end)
    {
        $stmt = $this->db->prepare(
            "SELECT ep.*, u.name as employee_name FROM employee_payments ep
             JOIN users u ON u.id = ep.user_id
             WHERE DATE(ep.paid_at) BETWEEN ? AND ? ORDER BY ep.paid_at"
        );
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll();
    }
}
