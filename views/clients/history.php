<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Historial de Ventas - <?= h($client['full_name']) ?></span>
        <a href="<?= BASE_URL ?>/clients" class="btn btn-sm btn-secondary">Volver</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th># Venta</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Total USD</th>
                        <th>Total Bs</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>Este cliente no tiene ventas registradas
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?= $sale['id'] ?></td>
                        <td><?= format_datetime($sale['created_at']) ?></td>
                        <td><?= get_status_badge($sale['sale_type']) ?></td>
                        <td><?= format_usd($sale['total_usd']) ?></td>
                        <td><?= format_bs($sale['total_bs']) ?></td>
                        <td><?= get_status_badge($sale['status']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/sales/detail/<?= $sale['id'] ?>" class="btn btn-sm btn-outline-info btn-icon">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
