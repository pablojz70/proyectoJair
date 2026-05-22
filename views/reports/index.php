<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase">Ventas Hoy</small>
                        <h4 class="mt-1 mb-0"><?= format_usd($todaySales['total_usd']) ?></h4>
                        <small><?= $todaySales['count'] ?> ventas</small>
                    </div>
                    <i class="bi bi-calendar-day display-6 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase">Ventas Semana</small>
                        <h4 class="mt-1 mb-0"><?= format_usd($weekSales['total_usd']) ?></h4>
                        <small><?= $weekSales['count'] ?> ventas</small>
                    </div>
                    <i class="bi bi-calendar-week display-6 text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted text-uppercase">Ventas Mes</small>
                        <h4 class="mt-1 mb-0"><?= format_usd($monthSales['total_usd']) ?></h4>
                        <small><?= $monthSales['count'] ?> ventas</small>
                    </div>
                    <i class="bi bi-calendar-month display-6 text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Ventas por Tipo</div>
            <div class="card-body">
                <canvas id="salesByTypeChart" height="200"></canvas>
                <div class="mt-2">
                    <?php foreach ($salesByType as $st): ?>
                    <div class="d-flex justify-content-between">
                        <span><?= ucfirst($st['sale_type']) ?></span>
                        <span class="fw-bold"><?= format_usd($st['total']) ?> (<?= $st['count'] ?>)</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Ventas por Empleado</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>#</th><th>Empleado</th><th>Ventas</th><th>Total USD</th></tr></thead>
                    <tbody>
                        <?php if (empty($employeeSales)): ?>
                        <tr><td colspan="4" class="text-center text-muted">Sin datos de empleados</td></tr>
                        <?php else: ?>
                        <?php foreach ($employeeSales as $i => $es): ?>
                        <?php if ($es['sale_count'] <= 0) continue; ?>
                        <tr><td><?= $i + 1 ?></td><td><?= h($es['name']) ?></td><td><?= $es['sale_count'] ?></td><td><?= format_usd($es['total']) ?></td></tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header">Top 5 Clientes</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Ventas</th>
                            <th>Total USD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topClients as $i => $client): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= h($client['full_name']) ?></td>
                            <td><?= $client['sale_count'] ?></td>
                            <td><?= format_usd($client['total']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex gap-2">
                <a href="<?= BASE_URL ?>/reports" class="btn btn-sm <?= $action === 'index' ? 'btn-primary' : 'btn-outline-primary' ?>">Resumen</a>
                <a href="<?= BASE_URL ?>/reports/profitability" class="btn btn-sm btn-outline-primary">Rentabilidad</a>
                <a href="<?= BASE_URL ?>/reports/inventory" class="btn btn-sm btn-outline-primary">Inventario</a>
                <a href="<?= BASE_URL ?>/reports/collections" class="btn btn-sm btn-outline-primary">Cobranzas</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('salesByTypeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_map(function($st) { return ucfirst($st['sale_type']); }, $salesByType)) ?>,
                datasets: [{
                    data: <?= json_encode(array_map(function($st) { return (float)$st['total']; }, $salesByType)) ?>,
                    backgroundColor: ['#0d6efd', '#6f42c1']
                }]
            }
        });
    }
});
</script>
