<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-people me-2"></i>Empleados</span>
        <div>
            <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nuevo Empleado</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Buscar..." value="<?= h($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                <a href="<?= BASE_URL ?>/employees" class="btn btn-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Nombre</th><th>Usuario</th><th>Telefono</th><th>Email</th><th>Comision</th><th>Bono c/10</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No hay empleados</td></tr>
                    <?php else: ?>
                    <?php foreach ($employees as $e): ?>
                    <tr>
                        <td><?= h($e['name']) ?></td>
                        <td><strong><?= h($e['username']) ?></strong></td>
                        <td><?= h($e['phone'] ?? '-') ?></td>
                        <td><?= h($e['email']) ?></td>
                        <td><?= $e['commission_rate'] ?>%</td>
                        <td>$<?= number_format($e['bonus_per_10_units'], 2) ?></td>
                        <td><?= $e['employee_status'] === 'activo' ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/users/edit/<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary btn-icon" title="Editar"><i class="bi bi-pencil"></i></a>
                            <a href="<?= BASE_URL ?>/employees/history/<?= $e['id'] ?>" class="btn btn-sm btn-outline-info btn-icon" title="Historial"><i class="bi bi-clock-history"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <hr>
        <h6 class="fw-bold"><i class="bi bi-graph-up me-2"></i>Reporte de Produccion</h6>
        <form method="GET" class="row g-2 mb-2">
            <div class="col-auto">
                <select name="period" class="form-select form-select-sm">
                    <option value="all" <?= ($_GET['period'] ?? '') === 'all' ? 'selected' : '' ?>>Todo</option>
                    <option value="today" <?= ($_GET['period'] ?? '') === 'today' ? 'selected' : '' ?>>Hoy</option>
                    <option value="week" <?= ($_GET['period'] ?? '') === 'week' ? 'selected' : '' ?>>Esta Semana</option>
                    <option value="month" <?= ($_GET['period'] ?? '') === 'month' ? 'selected' : '' ?>>Este Mes</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary">Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead><tr><th>Empleado</th><th>Producciones</th><th>Unidades</th><th>Bonos</th></tr></thead>
                <tbody>
                    <?php if (empty($report)): ?>
                    <tr><td colspan="4" class="text-center text-muted">Sin datos de produccion</td></tr>
                    <?php else: ?>
                    <?php foreach ($report as $r): ?>
                    <tr>
                        <td><?= h($r['name']) ?></td>
                        <td><?= $r['production_count'] ?></td>
                        <td><?= $r['total_qty'] ?></td>
                        <td>$<?= number_format($r['total_bonus'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
