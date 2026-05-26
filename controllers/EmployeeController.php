<?php
class EmployeeController
{
    private $model;
    private $productModel;

    public function __construct()
    {
        Session::requireLogin();
        $this->model = new Employee();
        $this->productModel = new Product();
    }

    public function index()
    {
        Session::requireAdmin();
        $pageTitle = 'Empleados';
        $search = $_GET['search'] ?? '';
        $employees = $this->model->getAll($search);
        $period = $_GET['period'] ?? 'all';
        $report = $this->model->getProductionReport($period);
        $settings = $this->model->getSettings();

        ob_start();
        require __DIR__ . '/../views/employees/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function production()
    {
        $employees = $this->model->getAll();
        $compoundProducts = $this->productModel->getAll(null, 'compuesto');
        $settings = $this->model->getSettings();
        $pageTitle = 'Registrar Produccion';

        ob_start();
        require __DIR__ . '/../views/employees/production.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function doProduction()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/employees/production');

        $employeeId = (int) ($_POST['employee_id'] ?? 0);
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $producedAt = $_POST['produced_at'] ?? date('Y-m-d');

        if (!$employeeId || !$productId || $quantity <= 0) {
            alert_error('Datos de produccion invalidos');
            redirect(BASE_URL . '/employees/production');
        }

        $missing = $this->productModel->checkRecipeStock($productId, $quantity);
        if (!empty($missing)) {
            $msg = 'Stock insuficiente de materias primas: ';
            foreach ($missing as $m) {
                $msg .= "{$m['name']} (necesita {$m['needed']} {$m['unit']}, tiene {$m['available']}), ";
            }
            alert_error(rtrim($msg, ', '));
            redirect(BASE_URL . '/employees/production');
        }

        try {
            $this->model->addProduction([
                'user_id' => $employeeId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'produced_at' => $producedAt,
                'notes' => trim($_POST['notes'] ?? ''),
            ]);
            alert_success("Produccion registrada. Stock de materias primas actualizado.");
        } catch (Exception $e) {
            alert_error('Error: ' . $e->getMessage());
        }
        redirect(BASE_URL . '/employees/production');
    }

    public function history()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) redirect(BASE_URL . '/employees');

        $employee = $this->model->findById($id);
        if (!$employee) { alert_error('Empleado no encontrado'); redirect(BASE_URL . '/employees'); }

        $production = $this->model->getProduction($id, 200);
        $payments = $this->model->getPayments($id, 200);
        $accumulated = $this->model->getAccumulatedUnits($id);
        $todayStats = $this->model->getProductionStats($id, 'today');
        $weekStats = $this->model->getProductionStats($id, 'week');
        $monthStats = $this->model->getProductionStats($id, 'month');
        $pageTitle = 'Historial: ' . $employee['name'];

        ob_start();
        require __DIR__ . '/../views/employees/history.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function payments()
    {
        Session::requireAdmin();
        $pageTitle = 'Pagos a Empleados';
        $employees = $this->model->getAll();
        $allPayments = $this->model->getPayments(null, 200);

        ob_start();
        require __DIR__ . '/../views/employees/payments.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function settings()
    {
        Session::requireAdmin();
        $pageTitle = 'Configuracion de Empleados';
        $settings = $this->model->getSettings();

        ob_start();
        require __DIR__ . '/../views/employees/settings.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function saveSettings()
    {
        Session::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/employees/settings');

        $this->model->saveSettings([
            'commission_rate' => (float) ($_POST['commission_rate'] ?? 0),
            'bonus_amount' => (float) ($_POST['bonus_amount'] ?? 0),
            'bonus_every_units' => (int) ($_POST['bonus_every_units'] ?? 10),
        ]);

        alert_success('Configuracion guardada exitosamente');
        redirect(BASE_URL . '/employees/settings');
    }

    public function doPayment()
    {
        Session::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/employees/payments');

        $this->model->addPayment([
            'user_id' => (int) ($_POST['employee_id'] ?? 0),
            'payment_type' => $_POST['payment_type'] ?? 'comision',
            'amount_usd' => (float) ($_POST['amount_usd'] ?? 0),
            'period_start' => $_POST['period_start'] ?? date('Y-m-d'),
            'period_end' => $_POST['period_end'] ?? date('Y-m-d'),
            'notes' => trim($_POST['notes'] ?? ''),
        ]);
        alert_success('Pago registrado');
        redirect(BASE_URL . '/employees/payments');
    }
}
