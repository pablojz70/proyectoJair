<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calculator me-2"></i>Periodo: <?= format_date($period['period_start']) ?> - <?= format_date($period['period_end']) ?></span>
                <div>
                    <a href="<?= BASE_URL ?>/finances/recalculate/<?= $period['id'] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Recalcular?')"><i class="bi bi-arrow-clockwise me-1"></i>Recalcular</a>
                    <a href="<?= BASE_URL ?>/finances" class="btn btn-sm btn-secondary">Volver</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card stat-card primary"><div class="card-body">
                            <small>Ventas</small>
                            <h4><?= format_usd($period['total_sales_usd']) ?></h4>
                        </div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card danger" style="border-left-color:var(--bs-danger)"><div class="card-body">
                            <small>Gastos</small>
                            <h4 class="text-danger"><?= format_usd($period['total_expenses_usd']) ?></h4>
                        </div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card success"><div class="card-body">
                            <small>Ganancia Bruta</small>
                            <h4><?= format_usd($period['gross_profit_usd']) ?></h4>
                        </div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card warning"><div class="card-body">
                            <small>Comision Socio (10%)</small>
                            <h4><?= format_usd($period['commission_10pct_usd']) ?></h4>
                        </div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card info"><div class="card-body">
                            <small>Ganancia Neta</small>
                            <h4><?= format_usd($period['net_profit_usd']) ?></h4>
                        </div></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success"><div class="card-body">
                            <small>Disponible para distribuir</small>
                            <h4><?= format_usd($period['net_profit_usd'] - $period['savings_usd'] - $period['dividends_usd'] - $period['other_allocations_usd']) ?></h4>
                        </div></div>
                    </div>
                </div>

                <hr>
                <h6 class="fw-bold">Distribucion de Ganancias</h6>
                <form method="POST" action="<?= BASE_URL ?>/finances/updateAllocations/<?= $period['id'] ?>">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Ahorros ($)</label>
                            <input type="number" name="savings_usd" class="form-control" value="<?= $period['savings_usd'] ?>" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dividendos ($)</label>
                            <input type="number" name="dividends_usd" class="form-control" value="<?= $period['dividends_usd'] ?>" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Otros ($)</label>
                            <input type="number" name="other_allocations_usd" class="form-control" value="<?= $period['other_allocations_usd'] ?>" step="0.01" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notes" class="form-control" rows="2"><?= h($period['notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Guardar Distribucion</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-list me-2"></i>Gastos</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Tipo</th><th>Total</th></tr></thead>
                    <tbody>
                        <tr><td>Materia Prima</td><td class="text-danger"><?= format_usd($expenseTotals['materia_prima']) ?></td></tr>
                        <tr><td>Empleados</td><td class="text-danger"><?= format_usd($expenseTotals['empleado']) ?></td></tr>
                        <tr><td>Otros</td><td class="text-danger"><?= format_usd($expenseTotals['otro']) ?></td></tr>
                    </tbody>
                    <tfoot><tr class="fw-bold"><td>Total</td><td class="text-danger"><?= format_usd($expenseTotals['total']) ?></td></tr></tfoot>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>Pagos Empleados</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Empleado</th><th>Monto</th></tr></thead>
                    <tbody>
                        <?php if (empty($employeePayments)): ?>
                        <tr><td colspan="2" class="text-center text-muted py-2">Sin pagos</td></tr>
                        <?php else: ?>
                        <?php foreach ($employeePayments as $ep): ?>
                        <tr><td><?= h($ep['employee_name']) ?></td><td>$<?= number_format($ep['amount_usd'], 2) ?></td></tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
