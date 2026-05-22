<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-person me-2"></i><?= h($employee['name']) ?> (<?= h($employee['username']) ?>)</span>
                <a href="<?= BASE_URL ?>/employees" class="btn btn-sm btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card stat-card primary"><div class="card-body">
                            <small class="text-muted">Unidades Producidas</small>
                            <h4><?= $accumulated ?></h4>
                        </div></div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card success"><div class="card-body">
                            <small class="text-muted">Hoy</small>
                            <h4><?= $todayStats['total_qty'] ?> u.</h4>
                            <small>$<?= number_format($todayStats['total_bonus'], 2) ?></small>
                        </div></div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card info"><div class="card-body">
                            <small class="text-muted">Esta Semana</small>
                            <h4><?= $weekStats['total_qty'] ?> u.</h4>
                            <small>$<?= number_format($weekStats['total_bonus'], 2) ?></small>
                        </div></div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card warning"><div class="card-body">
                            <small class="text-muted">Este Mes</small>
                            <h4><?= $monthStats['total_qty'] ?> u.</h4>
                            <small>$<?= number_format($monthStats['total_bonus'], 2) ?></small>
                        </div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card"><div class="card-header">Produccion</div><div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Producto</th><th>Cant</th><th>Bono</th><th>Fecha</th></tr></thead>
                <tbody>
                    <?php if (empty($production)): ?>
                    <tr><td colspan="4" class="text-center py-3 text-muted">Sin produccion registrada</td></tr>
                    <?php else: ?>
                    <?php foreach ($production as $p): ?>
                    <tr>
                        <td><?= h($p['product_name']) ?></td>
                        <td><?= $p['quantity'] ?></td>
                        <td>$<?= number_format($p['bonus_earned'], 2) ?></td>
                        <td><?= format_date($p['produced_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card"><div class="card-header">Pagos Recibidos</div><div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Tipo</th><th>Monto</th><th>Periodo</th><th>Fecha</th></tr></thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="4" class="text-center py-3 text-muted">Sin pagos registrados</td></tr>
                    <?php else: ?>
                    <?php foreach ($payments as $pm): ?>
                    <tr>
                        <td><span class="badge bg-<?= $pm['payment_type'] === 'bono' ? 'success' : ($pm['payment_type'] === 'comision' ? 'info' : 'secondary') ?>"><?= ucfirst($pm['payment_type']) ?></span></td>
                        <td class="fw-bold">$<?= number_format($pm['amount_usd'], 2) ?></td>
                        <td><small><?= format_date($pm['period_start']) ?> - <?= format_date($pm['period_end']) ?></small></td>
                        <td><?= format_date($pm['paid_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>
