<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-<?= isset($product) ? 'pencil' : 'box' ?> me-2"></i>
                <?= isset($product) ? 'Editar Producto' : 'Nuevo Producto ' . (($type ?? 'simple') === 'compuesto' ? 'Compuesto' : 'Simple') ?>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/products/<?= isset($product) ? 'update/' . $product['id'] : 'store' ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="<?= $product['type'] ?? $type ?? 'simple' ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="name" class="form-control" value="<?= h($product['name'] ?? old('name')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripcion</label>
                        <textarea name="description" class="form-control" rows="2"><?= h($product['description'] ?? old('description')) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio de venta (USD) *</label>
                            <input type="number" name="sale_price_usd" class="form-control" value="<?= $product['sale_price_usd'] ?? old('sale_price_usd', '0') ?>" step="0.01" min="0" required>
                        </div>
                        <?php if (!isset($product) || $product['type'] === 'simple'): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?? old('stock', '0') ?>" step="0.01" min="0">
                        </div>
                        <?php endif; ?>
                        <?php if (isset($product) && $product['type'] === 'compuesto' && $product['production_cost_usd'] > 0): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Costo de produccion</label>
                            <input type="text" class="form-control" value="<?= format_usd($product['production_cost_usd']) ?>" disabled>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($product) && $product['type'] === 'compuesto'): ?>
                    <hr>
                    <h6 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Receta (ingredientes)</h6>
                    <div id="recipeContainer">
                        <?php if (isset($recipe) && !empty($recipe)): ?>
                        <?php foreach ($recipe as $i => $item): ?>
                        <div class="row g-2 mb-2 recipe-item">
                            <div class="col-md-5">
                                <select name="materials[]" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($materials as $mat): ?>
                                    <option value="<?= $mat['id'] ?>" <?= $item['raw_material_id'] == $mat['id'] ? 'selected' : '' ?>>
                                        <?= h($mat['name']) ?> (<?= format_usd($mat['unit_cost_usd']) ?>/<?= $mat['unit'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="quantities[]" class="form-control" value="<?= $item['quantity'] ?>" step="0.0001" min="0" placeholder="Cantidad" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" value="<?= h($item['unit']) ?>" disabled>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-recipe-item" <?= $i === 0 && count($recipe) === 1 ? 'disabled' : '' ?>>
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="row g-2 mb-2 recipe-item">
                            <div class="col-md-5">
                                <select name="materials[]" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($materials as $mat): ?>
                                    <option value="<?= $mat['id'] ?>" data-cost="<?= $mat['unit_cost_usd'] ?>" data-unit="<?= h($mat['unit']) ?>">
                                        <?= h($mat['name']) ?> (<?= format_usd($mat['unit_cost_usd']) ?>/<?= $mat['unit'] ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="quantities[]" class="form-control" step="0.0001" min="0" placeholder="Cantidad">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control unit-display" disabled placeholder="Unidad">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-recipe-item" disabled>
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success mt-2" id="addRecipeItem">
                        <i class="bi bi-plus-lg"></i> Agregar ingrediente
                    </button>
                    <?php endif; ?>

                    <?php if (!isset($product) && ($type ?? 'simple') === 'compuesto'): ?>
                    <hr>
                    <h6 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Receta (ingredientes)</h6>
                    <p class="text-muted small">Despues de crear el producto podras agregar ingredientes a la receta.</p>
                    <?php endif; ?>

                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i><?= isset($product) ? 'Actualizar' : 'Guardar' ?>
                    </button>
                    <a href="<?= BASE_URL ?>/products" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addRecipeItem');
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            const container = document.getElementById('recipeContainer');
            const template = container.querySelector('.recipe-item');
            const clone = template.cloneNode(true);
            clone.querySelectorAll('select, input').forEach(el => el.value = '');
            clone.querySelector('.remove-recipe-item').disabled = false;
            container.appendChild(clone);
            updateRemoveButtons();
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-recipe-item')) {
            const items = document.querySelectorAll('.recipe-item');
            if (items.length > 1) {
                e.target.closest('.recipe-item').remove();
                updateRemoveButtons();
            }
        }
    });

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.recipe-item');
        items.forEach((item, index) => {
            const btn = item.querySelector('.remove-recipe-item');
            if (btn) btn.disabled = items.length <= 1;
        });
    }
});
</script>
