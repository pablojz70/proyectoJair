<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-plus me-2"></i>Registrar Nuevo Vendedor
            </div>
            <div class="card-body">
                <?= flash_messages() ?>
                <form method="POST" action="<?= BASE_URL ?>/auth/doRegister">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" class="form-control" value="<?= h(old('name')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre de usuario</label>
                        <input type="text" name="username" class="form-control" value="<?= h(old('username')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electronico</label>
                        <input type="email" name="email" class="form-control" value="<?= h(old('email')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="phone" class="form-control" value="<?= h(old('phone')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contrasena</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar contrasena</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Registrar
                    </button>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
