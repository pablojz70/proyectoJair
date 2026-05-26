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
                    <?php if (isset($user)): ?>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Estado del empleado</label>
                        <select name="employee_status" class="form-select">
                            <option value="activo" <?= ($user['employee_status'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= ($user['employee_status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                        <small class="text-muted">Comision y bono se configuran en Empleados > Configuracion (aplica a todos).</small>
                    </div>
                    <?php endif; ?>
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
