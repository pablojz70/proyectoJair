<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-<?= isset($client) ? 'pencil' : 'person-plus' ?> me-2"></i>
                <?= isset($client) ? 'Editar Cliente' : 'Nuevo Cliente' ?>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/clients/<?= isset($client) ? 'update/' . $client['id'] : 'store' ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" name="full_name" class="form-control" value="<?= h($client['full_name'] ?? old('full_name')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cedula / RIF *</label>
                        <input type="text" name="cedula_rif" class="form-control" value="<?= h($client['cedula_rif'] ?? old('cedula_rif')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="phone" class="form-control" value="<?= h($client['phone'] ?? old('phone')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="notes" class="form-control" rows="3"><?= h($client['notes'] ?? old('notes')) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i><?= isset($client) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                    <a href="<?= BASE_URL ?>/clients" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
