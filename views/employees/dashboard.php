<div class="row g-3 mb-4">
    <div class="col-6 col-lg">
        <div class="card" style="border-left:4px solid #0dcaf0"><div class="card-body">
            <small class="text-muted text-uppercase">Tasa BCV</small>
            <h4 class="mt-1">Bs. <?= number_format($exchangeRate ?: 0, 2) ?></h4>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card stat-card primary"><div class="card-body">
            <small class="text-muted text-uppercase">Hoy</small>
            <h4 class="mt-1"><?= $todayStats['total_qty'] ?> unid.</h4>
            <small class="text-muted">$<?= number_format($todayStats['total_bonus'], 2) ?> en bonos</small>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card stat-card success"><div class="card-body">
            <small class="text-muted text-uppercase">Semana</small>
            <h4 class="mt-1"><?= $weekStats['total_qty'] ?> unid.</h4>
            <small class="text-muted">$<?= number_format($weekStats['total_bonus'], 2) ?> en bonos</small>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card stat-card info"><div class="card-body">
            <small class="text-muted text-uppercase">Mes</small>
            <h4 class="mt-1"><?= $monthStats['total_qty'] ?> unid.</h4>
            <small class="text-muted">$<?= number_format($monthStats['total_bonus'], 2) ?> en bonos</small>
        </div></div>
    </div>
    <div class="col-6 col-lg">
        <div class="card stat-card warning"><div class="card-body">
            <small class="text-muted text-uppercase">Total</small>
            <h4 class="mt-1"><?= $accumulated ?> unid.</h4>
            <small class="text-muted">Comision: <?= $settings['commission_rate'] ?>% | Bono c/<?= $settings['bonus_every_units'] ?>: $<?= number_format($settings['bonus_amount'], 2) ?></small>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card"><div class="card-header d-flex justify-content-between">
            <span><i class="bi bi-gear me-2"></i>Produccion Reciente</span>
            <a href="<?= BASE_URL ?>/employees/production" class="btn btn-sm btn-primary">Registrar</a>
        </div><div class="card-body p-0">
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
        <div class="card"><div class="card-header"><i class="bi bi-credit-card me-2"></i>Pagos Recibidos</div><div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th>Tipo</th><th>Monto</th><th>Fecha</th></tr></thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                    <tr><td colspan="3" class="text-center py-3 text-muted">Sin pagos aun</td></tr>
                    <?php else: ?>
                    <?php foreach ($payments as $pm): ?>
                    <tr>
                        <td><span class="badge bg-<?= $pm['payment_type'] === 'bono' ? 'success' : 'info' ?>"><?= ucfirst($pm['payment_type']) ?></span></td>
                        <td class="fw-bold">$<?= number_format($pm['amount_usd'], 2) ?></td>
                        <td><?= format_date($pm['paid_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div></div>
    </div>
</div>
