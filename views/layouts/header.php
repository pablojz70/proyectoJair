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
            </div>
            <?php
            $role = Session::get('user_role');
            $isVendedor = $role === 'vendedor';
            $isEmpleado = $role === 'empleado';
            $isAdmin = $role === 'admin';
            ?>
            <?php
            function subActive($ctr, $actions) {
                global $controller, $action;
                return in_array($controller, (array)$ctr) && in_array($action, (array)$actions) ? 'active' : '';
            }
            ?>
            <ul class="nav nav-pills flex-column mb-auto p-2">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-link <?= $controller === 'dashboard' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/dashboard.png" class="sidebar-icon me-2">Inicio
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/clients" class="nav-link <?= $controller === 'clients' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/clientes.png" class="sidebar-icon me-2">Clientes
                    </a>
                </li>

                <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menuProductos" role="button" aria-expanded="<?= in_array($controller, ['raw-materials','products']) ? 'true' : 'false' ?>">
                        <img src="<?= BASE_URL ?>/imagen/producto.png" class="sidebar-icon me-2">Productos <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <div class="collapse <?= in_array($controller, ['raw-materials','products']) ? 'show' : '' ?>" id="menuProductos">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item"><a href="<?= BASE_URL ?>/raw-materials" class="nav-link py-1 <?= $controller === 'raw-materials' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/materia.png" class="sidebar-icon-sm me-2">Materias Primas</a></li>
                            <li class="nav-item"><a href="<?= BASE_URL ?>/products" class="nav-link py-1 <?= $controller === 'products' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/producto.png" class="sidebar-icon-sm me-2">Productos</a></li>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <?php if ($isEmpleado): ?>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/employees/production" class="nav-link <?= subActive('employees', 'production') ?>">
                        <img src="<?= BASE_URL ?>/imagen/materia.png" class="sidebar-icon me-2">Produccion
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menuVentas" role="button" aria-expanded="<?= in_array($controller, ['sales']) ? 'true' : 'false' ?>">
                        <img src="<?= BASE_URL ?>/imagen/venta.png" class="sidebar-icon me-2">Ventas <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <div class="collapse <?= in_array($controller, ['sales']) ? 'show' : '' ?>" id="menuVentas">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item"><a href="<?= BASE_URL ?>/sales/register" class="nav-link py-1 <?= $controller === 'sales' && $action === 'register' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/venta.png" class="sidebar-icon-sm me-2">Nueva Venta</a></li>
                            <li class="nav-item"><a href="<?= BASE_URL ?>/sales" class="nav-link py-1 <?= $controller === 'sales' && $action === 'index' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/historial.png" class="sidebar-icon-sm me-2">Historial</a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/payments" class="nav-link <?= $controller === 'payments' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/pagos.png" class="sidebar-icon me-2">Pagos Clientes
                    </a>
                </li>

                <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/reports" class="nav-link <?= $controller === 'reports' ? 'active' : '' ?>">
                        <img src="<?= BASE_URL ?>/imagen/reportes.png" class="sidebar-icon me-2">Reportes
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menuEmpleados" role="button" aria-expanded="<?= in_array($controller, ['employees']) ? 'true' : 'false' ?>">
                        <img src="<?= BASE_URL ?>/imagen/empleado.png" class="sidebar-icon me-2">Empleados <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <div class="collapse <?= in_array($controller, ['employees']) ? 'show' : '' ?>" id="menuEmpleados">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item"><a href="<?= BASE_URL ?>/employees" class="nav-link py-1 <?= $controller === 'employees' && $action === 'index' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/empleado.png" class="sidebar-icon-sm me-2">Empleados</a></li>
                            <li class="nav-item"><a href="<?= BASE_URL ?>/employees/production" class="nav-link py-1 <?= $controller === 'employees' && $action === 'production' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/materia.png" class="sidebar-icon-sm me-2">Produccion</a></li>
                            <li class="nav-item"><a href="<?= BASE_URL ?>/employees/payments" class="nav-link py-1 <?= $controller === 'employees' && $action === 'payments' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/pagos.png" class="sidebar-icon-sm me-2">Pagos</a></li>
                            <li class="nav-item"><a href="<?= BASE_URL ?>/employees/settings" class="nav-link py-1 <?= $controller === 'employees' && $action === 'settings' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/reportes.png" class="sidebar-icon-sm me-2">Configuracion</a></li>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menuFinanzas" role="button" aria-expanded="<?= in_array($controller, ['finances']) ? 'true' : 'false' ?>">
                        <img src="<?= BASE_URL ?>/imagen/reportes.png" class="sidebar-icon me-2">Finanzas <i class="bi bi-chevron-down float-end"></i>
                    </a>
                    <div class="collapse <?= in_array($controller, ['finances']) ? 'show' : '' ?>" id="menuFinanzas">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item"><a href="<?= BASE_URL ?>/finances" class="nav-link py-1 <?= $controller === 'finances' && $action === 'index' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/reportes.png" class="sidebar-icon-sm me-2">Quincenas</a></li>
                            <li class="nav-item"><a href="<?= BASE_URL ?>/finances/expenses" class="nav-link py-1 <?= $controller === 'finances' && $action === 'expenses' ? 'active' : '' ?>"><img src="<?= BASE_URL ?>/imagen/pagos.png" class="sidebar-icon-sm me-2">Gastos</a></li>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <?php if ($isAdmin): ?>
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
                    <a href="#" id="installAppBtnMobile" class="btn btn-sm btn-outline-success d-md-none py-0 px-1" style="font-size:0.75rem;display:none" title="Instalar App">
                        <i class="bi bi-download"></i>
                    </a>
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
