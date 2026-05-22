<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shop display-4 text-primary"></i>
                    <h4 class="mt-2">Sistema de Ventas</h4>
                    <p class="text-muted">Inicia sesion para continuar</p>
                </div>

                <?= flash_messages() ?>

                <form method="POST" action="<?= BASE_URL ?>/auth/doLogin">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Usuario o Correo electronico</label>
                        <input type="text" name="login" class="form-control" placeholder="usuario o correo@ejemplo.com" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contrasena</label>
                        <input type="password" name="password" class="form-control" placeholder="Tu contrasena" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesion
                    </button>
                </form>
            </div>
        </div>
        <p class="text-center text-muted mt-3 small">
            &copy; <?= date('Y') ?> Sistema de Ventas
        </p>
    </div>
</div>
