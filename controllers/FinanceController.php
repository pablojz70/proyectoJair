<?php
class FinanceController
{
    private $model;
    private $expenseModel;

    public function __construct()
    {
        Session::requireAdmin();
        $this->model = new FinancePeriod();
        $this->expenseModel = new Expense();
    }

    public function index()
    {
        $pageTitle = 'Finanzas Quincenales';
        $periods = $this->model->getAll();

        // Current biweekly period
        $today = new DateTime();
        $day = (int) $today->format('d');
        $month = (int) $today->format('m');
        $year = (int) $today->format('Y');

        if ($day <= 15) {
            $periodStart = "{$year}-{$month}-01";
            $periodEnd = "{$year}-{$month}-15";
        } else {
            $lastDay = (int) $today->format('t');
            $periodStart = "{$year}-{$month}-16";
            $periodEnd = "{$year}-{$month}-{$lastDay}";
        }

        $current = $this->model->getCurrentOrCreate($periodStart, $periodEnd);
        $current = $this->model->findById($current['id']);

        $expenses = $this->expenseModel->getAll($periodStart, $periodEnd);
        $expenseTotals = $this->expenseModel->getTotalsByPeriod($periodStart, $periodEnd);
        $employeePayments = $this->model->getEmployeePaymentsInRange($periodStart, $periodEnd);
        $salesData = $this->model->getSalesInRangeRaw($periodStart, $periodEnd);
        $expensesData = $this->model->getExpensesInRangeRaw($periodStart, $periodEnd);

        ob_start();
        require __DIR__ . '/../views/finances/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function period()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) redirect(BASE_URL . '/finances');

        $period = $this->model->findById($id);
        if (!$period) { alert_error('Periodo no encontrado'); redirect(BASE_URL . '/finances'); }

        $expenses = $this->expenseModel->getAll($period['period_start'], $period['period_end']);
        $expenseTotals = $this->expenseModel->getTotalsByPeriod($period['period_start'], $period['period_end']);
        $employeePayments = $this->model->getEmployeePaymentsInRange($period['period_start'], $period['period_end']);
        $pageTitle = 'Periodo: ' . format_date($period['period_start']) . ' - ' . format_date($period['period_end']);

        ob_start();
        require __DIR__ . '/../views/finances/period.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function updateAllocations()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/finances');

        $this->model->updateAllocations($id, [
            'savings_usd' => (float) ($_POST['savings_usd'] ?? 0),
            'dividends_usd' => (float) ($_POST['dividends_usd'] ?? 0),
            'other_allocations_usd' => (float) ($_POST['other_allocations_usd'] ?? 0),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        alert_success('Distribucion guardada');
        redirect(BASE_URL . "/finances/period/{$id}");
    }

    public function recalculate()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) redirect(BASE_URL . '/finances');

        $this->model->recalculate($id);
        alert_success('Periodo recalculado');
        redirect(BASE_URL . "/finances/period/{$id}");
    }

    public function expenses()
    {
        $pageTitle = 'Gastos';
        $periodStart = $_GET['date_from'] ?? date('Y-m-01');
        $periodEnd = $_GET['date_to'] ?? date('Y-m-t');
        $type = $_GET['type'] ?? '';
        $expenses = $this->expenseModel->getAll($periodStart, $periodEnd, $type);

        ob_start();
        require __DIR__ . '/../views/finances/expenses.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function storeExpense()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/finances/expenses');

        $this->expenseModel->create([
            'type' => $_POST['type'] ?? 'otro',
            'description' => trim($_POST['description'] ?? ''),
            'amount_usd' => (float) ($_POST['amount_usd'] ?? 0),
            'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        alert_success('Gasto registrado');
        redirect(BASE_URL . '/finances/expenses');
    }

    public function editExpense()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) redirect(BASE_URL . '/finances/expenses');
        $expense = $this->expenseModel->findById($id);
        if (!$expense) { alert_error('Gasto no encontrado'); redirect(BASE_URL . '/finances/expenses'); }

        $pageTitle = 'Editar Gasto';
        ob_start();
        require __DIR__ . '/../views/finances/expense_form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function updateExpense()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/finances/expenses');

        $this->expenseModel->update($id, [
            'type' => $_POST['type'] ?? 'otro',
            'description' => trim($_POST['description'] ?? ''),
            'amount_usd' => (float) ($_POST['amount_usd'] ?? 0),
            'expense_date' => $_POST['expense_date'] ?? date('Y-m-d'),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        alert_success('Gasto actualizado');
        redirect(BASE_URL . '/finances/expenses');
    }

    public function deleteExpense()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) redirect(BASE_URL . '/finances/expenses');
        $this->expenseModel->delete($id);
        alert_success('Gasto eliminado');
        redirect(BASE_URL . '/finances/expenses');
    }
}
