<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calculator me-2"></i>Periodo Actual: <?= format_date($periodStart) ?> - <?= format_date($periodEnd) ?></span>
        <a href="<?= BASE_URL ?>/finances/period/<?= $current['id'] ?>" class="btn btn-sm btn-primary">Ver Detalle</a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card stat-card primary"><div class="card-body">
                    <small class="text-muted">Ventas</small>
                    <h4><?= format_usd($current['total_sales_usd']) ?></h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card danger" style="border-left-color:var(--bs-danger)"><div class="card-body">
                    <small class="text-muted">Gastos</small>
                    <h4 class="text-danger"><?= format_usd($current['total_expenses_usd']) ?></h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card success"><div class="card-body">
                    <small class="text-muted">Ganancia Bruta</small>
                    <h4><?= format_usd($current['gross_profit_usd']) ?></h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card warning"><div class="card-body">
                    <small class="text-muted">Comision Socio (10%)</small>
                    <h4><?= format_usd($current['commission_10pct_usd']) ?></h4>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card info"><div class="card-body">
                    <small class="text-muted">Ganancia Neta</small>
                    <h4><?= format_usd($current['net_profit_usd']) ?></h4>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card" style="border-left:4px solid #198754"><div class="card-body">
                    <small class="text-muted">Ahorros</small>
                    <h4 class="text-success"><?= format_usd($current['savings_usd']) ?></h4>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card" style="border-left:4px solid #0dcaf0"><div class="card-body">
                    <small class="text-muted">Dividendos</small>
                    <h4 class="text-info"><?= format_usd($current['dividends_usd']) ?></h4>
                </div></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card"><div class="card-header d-flex justify-content-between">
            <span><i class="bi bi-list me-2"></i>Gastos del Periodo</span>
            <a href="<?= BASE_URL ?>/finances/expenses" class="btn btn-sm btn-outline-primary">Ver Todos</a>
        </div><div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Descripcion</th><th>Tipo</th><th>Monto</th></tr></thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                    <tr><td colspan="3" class="text-center py-3 text-muted">Sin gastos en este periodo</td></tr>
                    <?php else: ?>
                    <?php foreach ($expenses as $ex): ?>
                    <tr>
                        <td><?= h($ex['description']) ?></td>
                        <td><span class="badge bg-<?= $ex['type'] === 'materia_prima' ? 'warning' : ($ex['type'] === 'empleado' ? 'info' : 'secondary') ?>"><?= str_replace('_', ' ', $ex['type']) ?></span></td>
                        <td class="fw-bold text-danger">$<?= number_format($ex['amount_usd'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot><tr class="fw-bold"><td colspan="2">Total Gastos</td><td class="text-danger">$<?= number_format($expenseTotals['total'], 2) ?></td></tr></tfoot>
            </table>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card"><div class="card-header"><i class="bi bi-people me-2"></i>Pagos a Empleados</div><div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Empleado</th><th>Tipo</th><th>Monto</th></tr></thead>
                <tbody>
                    <?php if (empty($employeePayments)): ?>
                    <tr><td colspan="3" class="text-center py-3 text-muted">Sin pagos a empleados</td></tr>
                    <?php else: ?>
                    <?php foreach ($employeePayments as $ep): ?>
                    <tr>
                        <td><?= h($ep['employee_name']) ?></td>
                        <td><span class="badge bg-<?= $ep['payment_type'] === 'bono' ? 'success' : 'info' ?>"><?= ucfirst($ep['payment_type']) ?></span></td>
                        <td class="fw-bold">$<?= number_format($ep['amount_usd'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Periodos Anteriores</div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Periodo</th><th>Ventas</th><th>Gastos</th><th>Bruta</th><th>Comision</th><th>Neta</th><th>Ahorros</th><th>Dividendos</th></tr></thead>
            <tbody>
                <?php foreach ($periods as $p): ?>
                <tr>
                    <td><a href="<?= BASE_URL ?>/finances/period/<?= $p['id'] ?>"><?= format_date($p['period_start']) ?> - <?= format_date($p['period_end']) ?></a></td>
                    <td><?= format_usd($p['total_sales_usd']) ?></td>
                    <td class="text-danger"><?= format_usd($p['total_expenses_usd']) ?></td>
                    <td><?= format_usd($p['gross_profit_usd']) ?></td>
                    <td><?= format_usd($p['commission_10pct_usd']) ?></td>
                    <td class="fw-bold"><?= format_usd($p['net_profit_usd']) ?></td>
                    <td><?= format_usd($p['savings_usd']) ?></td>
                    <td><?= format_usd($p['dividends_usd']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
