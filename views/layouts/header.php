<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? h($pageTitle) . ' - ' : '' ?>Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.php">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
</head>
<body>
    <?php if (Session::isLoggedIn()):
    $controller = $GLOBALS['controller'] ?? 'dashboard';
    $action = $GLOBALS['action'] ?? 'index';
    ?>
    <div id="sidebarOverlay"></div>
    <div class="d-flex" id="wrapper">
        <div class="sidebar sidebar-gradient" id="sidebar">
            <div class="sidebar-header p-3 border-bottom border-secondary">
                <h5 class="mb-0"><img src="<?= BASE_URL ?>/imagen/ventas..png" class="sidebar-icon me-2">Ventas</h5>
                <a href="<?= BASE_URL ?>/auth/profile" class="text-decoration-none text-dark small d-block mt-1">
                    <img src="<?= BASE_URL ?>/imagen/usuarios.png" class="sidebar-icon-sm me-1"><?= h(Session::get('user_name')) ?>
                </a>
            </div>
            <ul class="nav nav-pills flex-column mb-auto p-2">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= $controller === 'dashboard' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/dashboard.png" class="sidebar-icon me-2">Inicio
                    </a>
                </li>

                <?php if (Session::get('user_role') !== 'empleado'): ?>

                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Clientes</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/clients" class="nav-link <?= $controller === 'clients' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/clientes.png" class="sidebar-icon me-2">Clientes
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Productos</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/raw-materials" class="nav-link <?= $controller === 'raw-materials' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/materia.png" class="sidebar-icon me-2">Materias Primas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/products" class="nav-link <?= $controller === 'products' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/producto.png" class="sidebar-icon me-2">Productos
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Ventas</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/sales/register" class="nav-link <?= $controller === 'sales' && $action === 'register' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/venta.png" class="sidebar-icon me-2">Nueva Venta
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/sales" class="nav-link <?= $controller === 'sales' && $action === 'index' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/historial.png" class="sidebar-icon me-2">Historial
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Cobranzas</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/payments" class="nav-link <?= $controller === 'payments' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/pagos.png" class="sidebar-icon me-2">Pagos Clientes
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Reportes</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/reports" class="nav-link <?= $controller === 'reports' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/reportes.png" class="sidebar-icon me-2">Reportes
                    </a>
                </li>

                <?php endif; ?>

                <?php if (Session::get('user_role') === 'empleado'): ?>
                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Produccion</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/employees/production" class="nav-link <?= $controller === 'employees' && $action === 'production' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/materia.png" class="sidebar-icon me-2">Registrar
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Session::isAdmin()): ?>
                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Empleados</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/employees" class="nav-link <?= $controller === 'employees' && $action === 'index' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/empleado.png" class="sidebar-icon me-2">Empleados
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/employees/production" class="nav-link <?= $controller === 'employees' && $action === 'production' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/materia.png" class="sidebar-icon me-2">Produccion
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/employees/payments" class="nav-link <?= $controller === 'employees' && $action === 'payments' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/pagos.png" class="sidebar-icon me-2">Pagos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/employees/settings" class="nav-link <?= $controller === 'employees' && $action === 'settings' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/reportes.png" class="sidebar-icon me-2">Configuracion
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Finanzas</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/finances" class="nav-link <?= $controller === 'finances' && $action === 'index' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/reportes.png" class="sidebar-icon me-2">Quincenas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/finances/expenses" class="nav-link <?= $controller === 'finances' && $action === 'expenses' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/pagos.png" class="sidebar-icon me-2">Gastos
                    </a>
                </li>

                <li class="nav-item mt-2">
                    <small class="text-uppercase px-2 fw-bold" style="color:#2c3e50">Admin</small>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/users" class="nav-link <?= $controller === 'users' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/usuarios.png" class="sidebar-icon me-2">Usuarios
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="p-3 border-top border-secondary">
                <a href="#" id="installAppBtn" class="btn btn-outline-light btn-sm w-100 mb-2" style="display:none">
                    <i class="bi bi-download me-2"></i>Instalar App
                </a>
                <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline-light btn-sm w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesion
                </a>
            </div>
        </div>

        <div id="page-content-wrapper" class="w-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom px-3">
                <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <span class="ms-2 fw-bold text-truncate" style="max-width:40vw">
                    <?php
                    $iconMap = [
                        'dashboard' => 'dashboard',
                        'clients' => 'clientes',
                        'raw-materials' => 'materia',
                        'products' => 'producto',
                        'sales' => in_array($action, ['register']) ? 'venta' : 'historial',
                        'payments' => 'pagos',
                        'reports' => 'reportes',
                        'users' => 'usuarios',
                        'employees' => 'usuarios',
                        'finances' => in_array($action, ['expenses', 'editExpense']) ? 'pagos' : 'reportes',
                        'auth' => 'usuarios',
                    ];
                    $icon = $iconMap[$controller] ?? 'dashboard';
                    ?>
                    <img src="<?= BASE_URL ?>/imagen/<?= $icon ?>.png" class="sidebar-icon me-1">
                    <?= h($pageTitle ?? 'Inicio') ?>
                </span>
                <div class="ms-auto text-muted small d-flex align-items-center gap-1">
                    <span class="d-none d-sm-inline">
                        <img src="<?= BASE_URL ?>/imagen/usuarios.png" class="sidebar-icon-sm me-1"><?= h(Session::get('user_name')) ?>
                    </span>
                    <span class="badge bg-<?= Session::isAdmin() ? 'danger' : 'primary' ?> ms-1 d-none d-sm-inline">
                        <?= Session::isAdmin() ? 'Admin' : 'Vendedor' ?>
                    </span>
                    <span class="badge bg-<?= Session::isAdmin() ? 'danger' : 'primary' ?> d-sm-none" title="<?= h(Session::get('user_name')) ?>">
                        <i class="bi bi-person"></i>
                    </span>
                </div>
            </nav>
            <div class="container-fluid p-4">
                <?= flash_messages() ?>
    <?php else: ?>
    <div class="container">
    <?php endif; ?>
