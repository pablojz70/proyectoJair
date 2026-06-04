<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-cloud-arrow-down me-2"></i>Descargar Respaldo</div>
            <div class="card-body">
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-1"></i>
                    Se generara un archivo SQL con la estructura y datos de todas las tablas.
                </div>
                <div class="text-center">
                    <a href="<?= BASE_URL ?>/backup/download" class="btn btn-success">
                        <i class="bi bi-download me-2"></i>Descargar Respaldo
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-upload me-2"></i>Restaurar desde archivo</div>
            <div class="card-body">
                <p class="text-muted small">Selecciona un archivo SQL de respaldo (.sql) para restaurar la base de datos.</p>
                <p class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Esto sobrescribira los datos actuales.</p>
                <form method="POST" action="<?= BASE_URL ?>/backup/restore" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <input type="file" name="backup_file" class="form-control" accept=".sql" required>
                    </div>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Restaurar sobrescribira los datos actuales. Continuar?')">
                        <i class="bi bi-upload me-2"></i>Restaurar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Respaldos en el servidor</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Archivo</th><th>Tamano</th><th>Accion</th></tr></thead>
                        <tbody>
                            <?php if (empty($backups)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">No hay respaldos guardados</td></tr>
                            <?php else: ?>
                            <?php foreach ($backups as $b): ?>
                            <?php $name = basename($b); $size = filesize($b); ?>
                            <tr>
                                <td><small><?= h($name) ?></small></td>
                                <td><small><?= $size > 1024 ? round($size/1024, 1) . ' KB' : $size . ' bytes' ?></small></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/backup/download/<?= $name ?>" class="btn btn-sm btn-outline-primary btn-icon" title="Descargar"><i class="bi bi-download"></i></a>
                                    <a href="<?= BASE_URL ?>/backup/restore/<?= $name ?>" class="btn btn-sm btn-outline-warning btn-icon" onclick="return confirm('Restaurar este respaldo? Se sobrescribiran los datos actuales.')" title="Restaurar"><i class="bi bi-arrow-counterclockwise"></i></a>
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
</div>
