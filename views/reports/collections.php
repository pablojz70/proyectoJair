<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-credit-card me-2"></i>Cobranzas</span>
        <div>
            <a href="<?= BASE_URL ?>/reports" class="btn btn-sm btn-outline-primary">Resumen</a>
            <a href="<?= BASE_URL ?>/reports/profitability" class="btn btn-sm btn-outline-primary">Rentabilidad</a>
            <a href="<?= BASE_URL ?>/reports/inventory" class="btn btn-sm btn-outline-primary">Inventario</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card success">
                    <div class="card-body">
                        <small class="text-muted">Total Facturado a Credito</small>
                        <h4><?= format_usd($totalCredit) ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card primary">
                    <div class="card-body">
                        <small class="text-muted">Total Cobrado (General)</small>
                        <h4><?= format_usd($totalPayments['total_usd']) ?></h4>
                        <small><?= $totalPayments['count'] ?> pagos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card <?= $efficiency > 50 ? 'success' : 'warning' ?>">
                    <div class="card-body">
                        <small class="text-muted">Eficiencia de Cobranza</small>
                        <h4><?= number_format($efficiency, 1) ?>%</h4>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar <?= $efficiency > 50 ? 'bg-success' : 'bg-warning' ?>" role="progressbar" style="width: <?= min($efficiency, 100) ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Cobranza del Mes</div>
                    <div class="card-body">
                        <h3><?= format_usd($monthPayments['total_usd']) ?></h3>
                        <small class="text-muted"><?= $monthPayments['count'] ?> pagos este mes</small>
                        <div class="mt-3">
                            <strong>En Bolivares:</strong> <?= format_bs($monthPayments['total_bs']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Clientes Morosos</div>
                    <div class="card-body">
                        <?php if (empty($overdueClients)): ?>
                        <p class="text-success"><i class="bi bi-check-circle me-2"></i>No hay clientes morosos</p>
                        <?php else: ?>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Deudas</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($overdueClients as $oc): ?>
                                <tr class="<?= $oc['overdue'] ? 'table-danger' : '' ?>">
                                    <td><?= h($oc['client_name']) ?></td>
                                    <td><?= $oc['count'] ?></td>
                                    <td class="fw-bold"><?= format_usd($oc['total_debt']) ?></td>
                                    <td>
                                        <?php if ($oc['overdue']): ?>
                                        <span class="badge bg-danger">Vencido</span>
                                        <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
