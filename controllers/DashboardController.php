<?php
class DashboardController
{
    public function index()
    {
        Session::requireLogin();
        $pageTitle = 'Inicio';
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');

        if ($userRole === 'empleado') {
        $exchangeRate = ExchangeRate::getRateQuick();
            $employeeModel = new Employee();
            $employee = $employeeModel->findById($userId);
            $production = $employeeModel->getProduction($userId, 10);
            $payments = $employeeModel->getPayments($userId, 10);
            $accumulated = $employeeModel->getAccumulatedUnits($userId);
            $todayStats = $employeeModel->getProductionStats($userId, 'today');
            $weekStats = $employeeModel->getProductionStats($userId, 'week');
            $monthStats = $employeeModel->getProductionStats($userId, 'month');

            ob_start();
            require __DIR__ . '/../views/employees/dashboard.php';
            $content = ob_get_clean();
            require __DIR__ . '/../views/layouts/header.php';
            echo $content;
            require __DIR__ . '/../views/layouts/footer.php';
            return;
        }

            $exchangeRate = ExchangeRate::getRateQuick();
        $exchangeSource = 'api';
        if ($exchangeRate) {
            $stmt = $db->query("SELECT source FROM exchange_rates ORDER BY id DESC LIMIT 1");
            $last = $stmt->fetch();
            $exchangeSource = $last ? $last['source'] : 'api';
        }

        if ($userRole === 'admin') {
            $totalSales = $db->query("SELECT COUNT(*) as count, COALESCE(SUM(total_usd),0) as total FROM sales")->fetch();
            $totalClients = $db->query("SELECT COUNT(*) as count FROM clients")->fetch();
            $totalProducts = $db->query("SELECT COUNT(*) as count FROM products")->fetch();
            $totalVendors = $db->query("SELECT COUNT(*) as count FROM users WHERE role='vendedor'")->fetch();
            $pendingSales = $db->query("SELECT COUNT(*) as count, COALESCE(SUM(total_usd),0) as total FROM sales WHERE status='pendiente'")->fetch();
            $recentSales = $db->query("SELECT s.*, c.full_name as client_name FROM sales s JOIN clients c ON c.id = s.client_id ORDER BY s.created_at DESC LIMIT 5")->fetchAll();
        } else {
            $totalSales = $db->prepare("SELECT COUNT(*) as count, COALESCE(SUM(total_usd),0) as total FROM sales WHERE user_id = ?");
            $totalSales->execute([$userId]);
            $totalSales = $totalSales->fetch();

            $totalClients = $db->prepare("SELECT COUNT(*) as count FROM clients WHERE user_id = ?");
            $totalClients->execute([$userId]);
            $totalClients = $totalClients->fetch();

            $totalProducts = $db->prepare("SELECT COUNT(*) as count FROM products WHERE user_id = ?");
            $totalProducts->execute([$userId]);
            $totalProducts = $totalProducts->fetch();

            $pendingSales = $db->prepare("SELECT COUNT(*) as count, COALESCE(SUM(total_usd),0) as total FROM sales WHERE user_id = ? AND status='pendiente'");
            $pendingSales->execute([$userId]);
            $pendingSales = $pendingSales->fetch();

            $recentSales = $db->prepare("SELECT s.*, c.full_name as client_name FROM sales s JOIN clients c ON c.id = s.client_id WHERE s.user_id = ? ORDER BY s.created_at DESC LIMIT 5");
            $recentSales->execute([$userId]);
            $recentSales = $recentSales->fetchAll();

            $totalVendors = ['count' => 0];
        }

        ob_start();
        require __DIR__ . '/../views/dashboard/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
