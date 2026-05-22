<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-<?= isset($material) ? 'pencil' : 'box' ?> me-2"></i>
                <?= isset($material) ? 'Editar Materia Prima' : 'Nueva Materia Prima' ?>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/raw-materials/<?= isset($material) ? 'update/' . $material['id'] : 'store' ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="name" class="form-control" value="<?= h($material['name'] ?? old('name')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unidad de medida *</label>
                        <select name="unit" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit ?>" <?= (isset($material) && $material['unit'] === $unit) ? 'selected' : '' ?>>
                                <?= $unit ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock actual</label>
                            <input type="number" name="stock" class="form-control" value="<?= $material['stock'] ?? old('stock', '0') ?>" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Costo unitario (USD)</label>
                            <input type="number" name="unit_cost_usd" class="form-control" value="<?= $material['unit_cost_usd'] ?? old('unit_cost_usd', '0') ?>" step="0.0001" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock minimo para alerta</label>
                        <input type="number" name="min_stock" class="form-control" value="<?= $material['min_stock'] ?? old('min_stock', '5') ?>" step="0.01" min="0">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i><?= isset($material) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                    <a href="<?= BASE_URL ?>/raw-materials" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
