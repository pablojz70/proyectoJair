<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Registrar Gasto</div>
            <div class="card-body">
                <?= flash_messages() ?>
                <form method="POST" action="<?= BASE_URL ?>/finances/storeExpense">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select" required>
                            <option value="materia_prima">Materia Prima</option>
                            <option value="empleado">Empleado</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripcion</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monto (USD)</label>
                            <input type="number" name="amount_usd" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-2"></i>Registrar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span><i class="bi bi-list me-2"></i>Gastos</span>
                <form method="GET" class="d-flex gap-2">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="<?= h($_GET['date_from'] ?? date('Y-m-01')) ?>">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="<?= h($_GET['date_to'] ?? date('Y-m-t')) ?>">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="materia_prima" <?= ($_GET['type'] ?? '') === 'materia_prima' ? 'selected' : '' ?>>Materia Prima</option>
                        <option value="empleado" <?= ($_GET['type'] ?? '') === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                        <option value="otro" <?= ($_GET['type'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                    <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Fecha</th><th>Descripcion</th><th>Tipo</th><th>Monto</th><th>Accion</th></tr></thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">Sin gastos</td></tr>
                        <?php else: ?>
                        <?php foreach ($expenses as $ex): ?>
                        <tr>
                            <td><?= format_date($ex['expense_date']) ?></td>
                            <td><?= h($ex['description']) ?></td>
                            <td><span class="badge bg-<?= $ex['type'] === 'materia_prima' ? 'warning' : ($ex['type'] === 'empleado' ? 'info' : 'secondary') ?>"><?= str_replace('_', ' ', $ex['type']) ?></span></td>
                            <td class="fw-bold text-danger">$<?= number_format($ex['amount_usd'], 2) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/finances/editExpense/<?= $ex['id'] ?>" class="btn btn-sm btn-outline-primary btn-icon"><i class="bi bi-pencil"></i></a>
                                <a href="<?= BASE_URL ?>/finances/deleteExpense/<?= $ex['id'] ?>" class="btn btn-sm btn-outline-danger btn-icon" onclick="return confirm('Eliminar?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
