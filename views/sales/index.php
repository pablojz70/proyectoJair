<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i>Historial de Ventas</span>
        <a href="<?= BASE_URL ?>/sales/register" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Venta
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/sales" class="row g-2 mb-3">
            <div class="col-auto">
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?= h($_GET['date_from'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?= h($_GET['date_to'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    <option value="contado" <?= ($_GET['type'] ?? '') === 'contado' ? 'selected' : '' ?>>Contado</option>
                    <option value="credito" <?= ($_GET['type'] ?? '') === 'credito' ? 'selected' : '' ?>>Credito</option>
                </select>
            </div>
            <div class="col-auto">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="pagada" <?= ($_GET['status'] ?? '') === 'pagada' ? 'selected' : '' ?>>Pagada</option>
                    <option value="pendiente" <?= ($_GET['status'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                    <option value="cancelada" <?= ($_GET['status'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Empleado</th>
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
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>No hay ventas registradas
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?= $sale['id'] ?></td>
                        <td><?= h($sale['client_name']) ?></td>
                        <td><?= h($sale['employee_name'] ?? '-') ?></td>
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
