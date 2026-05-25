<?php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/Session.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/helpers/ExchangeRate.php';

spl_autoload_register(function ($class) {
    $modelPath = __DIR__ . '/models/' . $class . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
    }
});

Session::init();

define('BASE_URL', '/proyectoJair');

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$urlParts = explode('/', $url);

$GLOBALS['controller'] = !empty($urlParts[0]) ? $urlParts[0] : 'dashboard';
$GLOBALS['action'] = !empty($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

$_GET['params'] = $params;

$routes = [
    'auth' => 'AuthController',
    'login' => 'AuthController',
    'logout' => 'AuthController',
    'dashboard' => 'DashboardController',
    'clients' => 'ClientController',
    'products' => 'ProductController',
    'raw-materials' => 'RawMaterialController',
    'sales' => 'SaleController',
    'payments' => 'PaymentController',
    'reports' => 'ReportController',
    'users' => 'UserController',
    'employees' => 'EmployeeController',
    'finances' => 'FinanceController',
];

$actionMap = [
    'login' => 'login',
    'logout' => 'logout',
    'register' => 'register',
];

if (isset($actionMap[$GLOBALS['controller']])) {
    $GLOBALS['action'] = $actionMap[$GLOBALS['controller']];
}

if ($GLOBALS['controller'] === 'login' || $GLOBALS['controller'] === 'logout' || $GLOBALS['controller'] === 'register') {
    $GLOBALS['controller'] = 'auth';
}

$controller = $GLOBALS['controller'];
$action = $GLOBALS['action'];

$controllerName = $routes[$controller] ?? null;

if ($controllerName) {
    $filePath = __DIR__ . "/controllers/{$controllerName}.php";
    if (file_exists($filePath)) {
        require_once $filePath;
        $instance = new $controllerName();
        if (method_exists($instance, $action)) {
            $instance->$action();
        } elseif (method_exists($instance, 'index')) {
            $instance->index();
        } else {
            http_response_code(404);
            require __DIR__ . '/views/errors/404.php';
        }
    } else {
        http_response_code(404);
        require __DIR__ . '/views/errors/404.php';
    }
} else {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
}
