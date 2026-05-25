<?php
class ReportController
{
    private $saleModel;
    private $productModel;
    private $paymentModel;
    private $rawMaterialModel;
    private $employeeModel;

    public function __construct()
    {
        Session::requireLogin();
        $this->saleModel = new Sale();
        $this->productModel = new Product();
        $this->paymentModel = new Payment();
        $this->rawMaterialModel = new RawMaterial();
        $this->employeeModel = new Employee();
    }

    public function index()
    {
        $pageTitle = 'Reportes';
        $userId = Session::get('user_id');

        $todaySales = $this->saleModel->getTotalsByPeriod($userId, 'today');
        $weekSales = $this->saleModel->getTotalsByPeriod($userId, 'week');
        $monthSales = $this->saleModel->getTotalsByPeriod($userId, 'month');
        $salesByType = $this->saleModel->getByType($userId);
        $topClients = $this->saleModel->getTopClients($userId);

        $db = Database::getInstance();
        $employeeSales = $db->query("
            SELECT u.id, u.name, COUNT(s.id) as sale_count, COALESCE(SUM(s.total_usd),0) as total
            FROM users u
            LEFT JOIN sales s ON s.employee_id = u.id
            WHERE u.role = 'empleado'
            GROUP BY u.id ORDER BY total DESC
        ")->fetchAll();

        ob_start();
        require __DIR__ . '/../views/reports/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function profitability()
    {
        $pageTitle = 'Rentabilidad';
        $userId = Session::get('user_id');
        $products = $this->productModel->getAll($userId);

        $productSales = [];
        $db = Database::getInstance();

        foreach ($products as $product) {
            $sql = "SELECT COALESCE(SUM(si.quantity),0) as total_qty, COALESCE(SUM(si.subtotal_usd),0) as total_revenue
                    FROM sale_items si JOIN sales s ON s.id = si.sale_id WHERE si.product_id = ?";
            $params = [$product['id']];

            if ($userId && !Session::isAdmin()) {
                $sql .= " AND s.user_id = ?";
                $params[] = $userId;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $stats = $stmt->fetch();

            $cost = $product['production_cost_usd'] ?? 0;
            $profit = $stats['total_revenue'] - ($cost * $stats['total_qty']);
            $margin = $stats['total_revenue'] > 0 ? ($profit / $stats['total_revenue']) * 100 : 0;

            $productSales[] = [
                'name' => $product['name'],
                'type' => $product['type'],
                'qty' => $stats['total_qty'],
                'revenue' => $stats['total_revenue'],
                'cost' => $cost,
                'profit' => $profit,
                'margin' => $margin,
            ];
        }

        usort($productSales, function ($a, $b) {
            return $b['qty'] <=> $a['qty'];
        });

        $topSold = array_slice($productSales, 0, 10);

        usort($productSales, function ($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        $topProfitable = array_slice($productSales, 0, 10);

        $avgMargin = 0;
        $countWithMargin = 0;
        foreach ($productSales as $ps) {
            if ($ps['margin'] > 0) {
                $avgMargin += $ps['margin'];
                $countWithMargin++;
            }
        }
        $avgMargin = $countWithMargin > 0 ? $avgMargin / $countWithMargin : 0;

        ob_start();
        require __DIR__ . '/../views/reports/profitability.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function inventory()
    {
        $pageTitle = 'Inventario';
        $userId = Session::get('user_id');
        $materials = $this->rawMaterialModel->getAll($userId);
        $products = $this->productModel->getAll($userId, 'simple');
        $compoundProducts = $this->productModel->getAll($userId, 'compuesto');

        $nonProducible = [];
        $threshold = (float) ($_GET['threshold'] ?? 10);

        foreach ($compoundProducts as $cp) {
            $missing = $this->productModel->checkRecipeStock($cp['id'], 1);
            if (!empty($missing)) {
                $nonProducible[] = [
                    'product' => $cp['name'],
                    'missing' => $missing,
                ];
            }
        }

        ob_start();
        require __DIR__ . '/../views/reports/inventory.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function collections()
    {
        $pageTitle = 'Cobranzas';
        $userId = Session::get('user_id');
        $overdueClients = $this->paymentModel->getOverdueClients($userId);
        $monthPayments = $this->paymentModel->getPaymentStats($userId, 'month');
        $totalPayments = $this->paymentModel->getPaymentStats($userId);

        $db = Database::getInstance();
        $creditSql = "SELECT COALESCE(SUM(s.total_usd),0) as total_credit FROM sales s WHERE s.sale_type = 'credito'";
        $params = [];
        if ($userId && !Session::isAdmin()) {
            $creditSql .= " AND s.user_id = ?";
            $params[] = $userId;
        }
        $stmt = $db->prepare($creditSql);
        $stmt->execute($params);
        $totalCredit = $stmt->fetchColumn();

        $efficiency = $totalCredit > 0 ? ($totalPayments['total_usd'] / $totalCredit) * 100 : 0;

        ob_start();
        require __DIR__ . '/../views/reports/collections.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
