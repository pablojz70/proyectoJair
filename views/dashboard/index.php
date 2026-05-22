<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card" style="border-left:4px solid #0dcaf0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase">Tasa BCV</small>
                        <h4 class="mt-1 mb-0">Bs. <?= number_format($exchangeRate ?: 0, 2) ?></h4>
                        <small class="text-muted"><?= $exchangeSource === 'api' ? 'API automatica' : 'Manual' ?></small>
                    </div>
                    <i class="bi bi-currency-dollar display-6 text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase">Ventas Totales</small>
                        <h4 class="mt-1 mb-0"><?= format_usd($totalSales['total']) ?></h4>
                        <small class="text-muted"><?= $totalSales['count'] ?> ventas</small>
                    </div>
                    <i class="bi bi-cart display-6 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase">Clientes</small>
                        <h4 class="mt-1 mb-0"><?= $totalClients['count'] ?></h4>
                        <small class="text-muted">Registrados</small>
                    </div>
                    <i class="bi bi-people display-6 text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase">Pendientes</small>
                        <h4 class="mt-1 mb-0"><?= format_usd($pendingSales['total']) ?></h4>
                        <small class="text-muted"><?= $pendingSales['count'] ?> ventas</small>
                    </div>
                    <i class="bi bi-clock display-6 text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase">Productos</small>
                        <h4 class="mt-1 mb-0"><?= $totalProducts['count'] ?></h4>
                        <small class="text-muted">En catalogo</small>
                    </div>
                    <i class="bi bi-box display-6 text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($totalVendors['count'] > 0): ?>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase">Vendedores</small>
                        <h4 class="mt-1 mb-0"><?= $totalVendors['count'] ?></h4>
                        <small class="text-muted">Registrados</small>
                    </div>
                    <i class="bi bi-person-badge display-6 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Ventas Recientes</span>
        <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-primary">Ver todas</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Total USD</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentSales)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-3 text-muted">
                            <i class="bi bi-inbox me-2"></i>No hay ventas registradas
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recentSales as $sale): ?>
                    <tr>
                        <td><?= $sale['id'] ?></td>
                        <td><?= h($sale['client_name']) ?></td>
                        <td><?= get_status_badge($sale['sale_type']) ?></td>
                        <td><?= format_usd($sale['total_usd']) ?></td>
                        <td><?= get_status_badge($sale['status']) ?></td>
                        <td><?= format_datetime($sale['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
