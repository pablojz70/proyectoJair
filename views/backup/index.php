<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-cloud-arrow-down me-2"></i>Respaldo de Base de Datos</div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-1"></i>
                    Se generara un archivo SQL con la estructura y datos de todas las tablas.
                </div>

                <div class="text-center mb-4">
                    <a href="<?= BASE_URL ?>/backup/download" class="btn btn-success btn-lg">
                        <i class="bi bi-download me-2"></i>Descargar Respaldo
                    </a>
                </div>

                <hr>
                <h6 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Respaldos guardados en el servidor</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Archivo</th><th>Tamano</th><th>Accion</th></tr></thead>
                        <tbody>
                            <?php if (empty($backups)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">No hay respaldos guardados</td></tr>
                            <?php else: ?>
                            <?php foreach ($backups as $b): ?>
                            <?php $name = basename($b); $size = filesize($b); ?>
                            <tr>
                                <td><?= h($name) ?></td>
                                <td><?= $size > 1024 ? round($size/1024, 1) . ' KB' : $size . ' bytes' ?></td>
                                <td><a href="<?= BASE_URL ?>/backup/download/<?= $name ?>" class="btn btn-sm btn-outline-primary">Descargar</a></td>
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
