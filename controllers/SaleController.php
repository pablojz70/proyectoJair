<?php
class SaleController
{
    private $saleModel;
    private $productModel;
    private $clientModel;
    private $paymentModel;
    private $employeeModel;

    public function __construct()
    {
        Session::requireLogin();
        $this->saleModel = new Sale();
        $this->productModel = new Product();
        $this->clientModel = new Client();
        $this->paymentModel = new Payment();
        $this->employeeModel = new Employee();
    }

    public function index()
    {
        $pageTitle = 'Historial de Ventas';
        $userId = Session::get('user_id');
        $filters = [
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'type' => $_GET['type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'client_id' => $_GET['client_id'] ?? '',
        ];

        $sales = $this->saleModel->getAll($userId, $filters);

        ob_start();
        require __DIR__ . '/../views/sales/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function register()
    {
        $pageTitle = 'Nueva Venta';
        $userId = Session::get('user_id');
        $clients = $this->clientModel->getAll($userId);
        $products = $this->productModel->getAvailableProducts($userId);
        $exchangeRate = ExchangeRate::getRate();
        $employees = $this->employeeModel->getAll();

        ob_start();
        require __DIR__ . '/../views/sales/register.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/sales');
        }

        $userId = Session::get('user_id');
        $clientId = (int) ($_POST['client_id'] ?? 0);
        $employeeId = !empty($_POST['employee_id']) ? (int) $_POST['employee_id'] : null;
        $saleType = $_POST['sale_type'] ?? 'contado';
        $dueDate = $_POST['due_date'] ?? null;
        $exchangeRate = (float) ($_POST['exchange_rate'] ?? 0);
        $manualRate = (float) ($_POST['manual_rate'] ?? 0);

        if ($exchangeRate <= 0) {
            $exchangeRate = ExchangeRate::getRate();
        }

        if ($exchangeRate <= 0) {
            alert_error('No se pudo obtener la tasa de cambio. Ingresela manualmente.');
            redirect(BASE_URL . '/sales/register');
        }

        if ($manualRate > 0 && $manualRate !== $exchangeRate) {
            $exchangeRate = $manualRate;
            ExchangeRate::saveRate($exchangeRate, 'manual');
        }

        $productIds = $_POST['product_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];

        if (empty($clientId)) {
            alert_error('Debe seleccionar un cliente');
            redirect(BASE_URL . '/sales/register');
        }

        if (empty($productIds) || !is_array($productIds)) {
            alert_error('Debe agregar al menos un producto');
            redirect(BASE_URL . '/sales/register');
        }

        $items = [];
        $totalUsd = 0;

        foreach ($productIds as $i => $pid) {
            if (empty($pid)) continue;
            $qty = (float) ($quantities[$i] ?? 0);
            if ($qty <= 0) continue;

            $product = $this->productModel->findById($pid);
            if (!$product) continue;

            if ($product['type'] === 'simple' && $product['stock'] < $qty) {
                alert_error("Stock insuficiente de: {$product['name']}");
                redirect(BASE_URL . '/sales/register');
            }

            if ($product['type'] === 'compuesto') {
                $missing = $this->productModel->checkRecipeStock($pid, $qty);
                if (!empty($missing)) {
                    $msg = "No se puede vender {$product['name']}. Faltan: ";
                    foreach ($missing as $m) {
                        $msg .= "{$m['name']} (necesita {$m['needed']} {$m['unit']}, tiene {$m['available']}), ";
                    }
                    alert_error(rtrim($msg, ', '));
                    redirect(BASE_URL . '/sales/register');
                }
            }

            $subtotal = $product['sale_price_usd'] * $qty;
            $totalUsd += $subtotal;

            $items[] = [
                'product_id' => $pid,
                'product_type' => $product['type'],
                'quantity' => $qty,
                'unit_price_usd' => $product['sale_price_usd'],
                'subtotal_usd' => $subtotal,
            ];
        }

        if (empty($items)) {
            alert_error('No se pudieron procesar los productos seleccionados');
            redirect(BASE_URL . '/sales/register');
        }

        $totalBs = $totalUsd * $exchangeRate;
        $status = $saleType === 'contado' ? 'pagada' : 'pendiente';

        try {
            $saleId = $this->saleModel->create([
                'user_id' => $userId,
                'client_id' => $clientId,
                'employee_id' => $employeeId,
                'sale_type' => $saleType,
                'total_usd' => $totalUsd,
                'total_bs' => $totalBs,
                'exchange_rate' => $exchangeRate,
                'status' => $status,
                'due_date' => $saleType === 'credito' ? $dueDate : null,
                'items' => $items,
            ]);

            alert_success('Venta registrada exitosamente');
            redirect(BASE_URL . '/sales');
        } catch (Exception $e) {
            alert_error('Error al registrar venta: ' . $e->getMessage());
            redirect(BASE_URL . '/sales/register');
        }
    }

    public function detail()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/sales');
        }

        $sale = $this->saleModel->findById($id);
        if (!$sale) {
            alert_error('Venta no encontrada');
            redirect(BASE_URL . '/sales');
        }

        $items = $this->saleModel->getItems($id);
        $payments = $this->paymentModel->getBySale($id);
        $totalPaid = $this->paymentModel->getTotalPaid($id);

        $pageTitle = 'Detalle de Venta #' . $id;

        ob_start();
        require __DIR__ . '/../views/sales/detail.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function getProductInfo()
    {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) {
            json_response(['error' => 'ID requerido'], 400);
        }

        $product = $this->productModel->findById($id);
        if (!$product) {
            json_response(['error' => 'Producto no encontrado'], 404);
        }

        json_response([
            'id' => $product['id'],
            'name' => $product['name'],
            'type' => $product['type'],
            'sale_price_usd' => $product['sale_price_usd'],
            'stock' => $product['stock'],
            'production_cost' => $product['production_cost_usd'],
        ]);
    }

    public function getClientDebts()
    {
        $clientId = (int) ($_GET['client_id'] ?? 0);
        if (!$clientId) {
            json_response(['error' => 'Cliente requerido'], 400);
        }

        $userId = Session::get('user_id');
        $debts = $this->paymentModel->getDebtsByClient($clientId, $userId);

        $totalDebt = 0;
        foreach ($debts as $d) {
            $totalDebt += $d['total_usd'] - $d['paid'];
        }

        json_response([
            'debts' => $debts,
            'total_debt' => $totalDebt,
            'count' => count($debts),
        ]);
    }
}
