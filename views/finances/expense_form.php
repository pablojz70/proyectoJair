<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil me-2"></i>Editar Gasto</div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/finances/updateExpense/<?= $expense['id'] ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="type" class="form-select" required>
                            <option value="materia_prima" <?= $expense['type'] === 'materia_prima' ? 'selected' : '' ?>>Materia Prima</option>
                            <option value="empleado" <?= $expense['type'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                            <option value="otro" <?= $expense['type'] === 'otro' ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripcion</label>
                        <input type="text" name="description" class="form-control" value="<?= h($expense['description']) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monto (USD)</label>
                            <input type="number" name="amount_usd" class="form-control" value="<?= $expense['amount_usd'] ?>" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="expense_date" class="form-control" value="<?= $expense['expense_date'] ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="2"><?= h($expense['notes'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Actualizar</button>
                    <a href="<?= BASE_URL ?>/finances/expenses" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
