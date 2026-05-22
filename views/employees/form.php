<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-<?= isset($employee) ? 'pencil' : 'person-plus' ?> me-2"></i><?= isset($employee) ? 'Editar Empleado' : 'Nuevo Empleado' ?></div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/employees/<?= isset($employee) ? 'update/' . $employee['id'] : 'store' ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="<?= h($employee['name'] ?? old('name')) ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="phone" class="form-control" value="<?= h($employee['phone'] ?? old('phone')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= h($employee['email'] ?? old('email')) ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Comision sobre ventas (%)</label>
                            <input type="number" name="commission_rate" class="form-control" value="<?= $employee['commission_rate'] ?? old('commission_rate', '0') ?>" step="0.01" min="0" max="100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bono por cada 10 unidades ($)</label>
                            <input type="number" name="bonus_per_10_units" class="form-control" value="<?= $employee['bonus_per_10_units'] ?? old('bonus_per_10_units', '0') ?>" step="0.01" min="0">
                        </div>
                    </div>
                    <?php if (isset($employee)): ?>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="activo" <?= $employee['status'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $employee['status'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i><?= isset($employee) ? 'Actualizar' : 'Guardar' ?></button>
                    <a href="<?= BASE_URL ?>/employees" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
