<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-<?= isset($user) ? 'pencil' : 'person-plus' ?> me-2"></i>
                <?= isset($user) ? 'Editar Usuario' : 'Nuevo Usuario' ?>
            </div>
            <div class="card-body">
                <?= flash_messages() ?>
                <form method="POST" action="<?= BASE_URL ?>/users/<?= isset($user) ? 'update/' . $user['id'] : 'store' ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" class="form-control" value="<?= h($user['name'] ?? old('name')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre de usuario</label>
                        <input type="text" name="username" class="form-control" value="<?= h($user['username'] ?? old('username')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electronico</label>
                        <input type="email" name="email" class="form-control" value="<?= h($user['email'] ?? old('email')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="phone" class="form-control" value="<?= h($user['phone'] ?? old('phone')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select" required onchange="toggleEmployeeFields(this)">
                            <option value="vendedor" <?= (isset($user) && $user['role'] === 'vendedor') ? 'selected' : '' ?>>Vendedor</option>
                            <option value="empleado" <?= (isset($user) && $user['role'] === 'empleado') ? 'selected' : '' ?>>Empleado</option>
                            <option value="admin" <?= (isset($user) && $user['role'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </div>
                    <div id="employeeFields" style="<?= (isset($user) && $user['role'] === 'empleado') ? '' : 'display:none' ?>">
                        <hr>
                        <h6 class="fw-bold"><i class="bi bi-gear me-2"></i>Configuracion de Empleado</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Comision sobre ventas (%)</label>
                                <input type="number" name="commission_rate" class="form-control" value="<?= $user['commission_rate'] ?? old('commission_rate', '0') ?>" step="0.01" min="0" max="100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bono por cada 10 unidades ($)</label>
                                <input type="number" name="bonus_per_10_units" class="form-control" value="<?= $user['bonus_per_10_units'] ?? old('bonus_per_10_units', '0') ?>" step="0.01" min="0">
                            </div>
                        </div>
                        <?php if (isset($user)): ?>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="employee_status" class="form-select">
                                <option value="activo" <?= ($user['employee_status'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= ($user['employee_status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <?php if (isset($user)): ?>
                    <p class="text-muted small">Deja en blanco si no deseas cambiar la contrasena</p>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label"><?= isset($user) ? 'Nueva ' : '' ?>Contrasena</label>
                        <input type="password" name="password" class="form-control" minlength="6" <?= !isset($user) ? 'required' : '' ?>>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i><?= isset($user) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                    <a href="<?= BASE_URL ?>/users" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEmployeeFields(select) {
    document.getElementById('employeeFields').style.display = select.value === 'empleado' ? '' : 'none';
}
</script>
