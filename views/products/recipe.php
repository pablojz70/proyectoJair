<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-text me-2"></i>Receta: <?= h($product['name']) ?></span>
                <a href="<?= BASE_URL ?>/products" class="btn btn-sm btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Precio de venta (c/u):</strong> <?= format_usd($product['sale_price_usd']) ?><br>
                    <strong>Esta receta produce:</strong> <?= $product['recipe_yield'] ?? 1 ?> unidad(es)<br>
                    <strong>Costo total de la receta:</strong> <?= format_usd($productionCost * ($product['recipe_yield'] ?? 1)) ?><br>
                    <strong>Costo de produccion (c/u):</strong> <?= format_usd($productionCost) ?><br>
                    <?php if ($productionCost > 0): ?>
                    <strong>Margen (c/u):</strong>
                    <span class="<?= (($product['sale_price_usd'] - $productionCost) / $product['sale_price_usd'] * 100) < 10 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format(($product['sale_price_usd'] - $productionCost) / $product['sale_price_usd'] * 100, 1) ?>%
                    </span>
                    <?php if ($productionCost >= $product['sale_price_usd']): ?>
                    <div class="alert alert-danger mt-2 py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>El costo de produccion por unidad es mayor o igual al precio de venta!
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php $yield = (int) ($product['recipe_yield'] ?? 1); ?>
                <p class="text-muted small">Los ingredientes son para <?= $yield ?> unidad(es). Por unidad se usa 1/<?= $yield ?> de cada cantidad.</p>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Materia Prima</th>
                            <th>Para <?= $yield ?> unid.</th>
                            <th>Por unidad</th>
                            <th>Costo total</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recipe)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No hay ingredientes en la receta
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recipe as $item): ?>
                        <tr>
                            <td><?= h($item['material_name']) ?></td>
                            <td><?= $item['quantity'] ?> <?= h($item['unit']) ?></td>
                            <td><?= number_format($item['quantity'] / $yield, 4) ?> <?= h($item['unit']) ?></td>
                            <td><?= format_usd($item['quantity'] * $item['unit_cost_usd']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/products/removeRecipeItem/<?= $item['id'] ?>/<?= $product['id'] ?>"
                                   class="btn btn-sm btn-outline-danger btn-icon"
                                   onclick="return confirm('Eliminar <?= h($item['material_name']) ?> de la receta?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="5">Costo total receta: <?= format_usd($productionCost * $yield) ?> | Costo c/u: <?= format_usd($productionCost) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle me-2"></i>Agregar Ingrediente
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/products/addRecipeItem/<?= $product['id'] ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Materia Prima</label>
                        <select name="material_id" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($materials as $mat): ?>
                            <option value="<?= $mat['id'] ?>">
                                <?= h($mat['name']) ?> (Stock: <?= $mat['stock'] ?> <?= $mat['unit'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad por unidad</label>
                        <input type="number" name="quantity" class="form-control" step="0.0001" min="0" placeholder="Ej: 0.5" required>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Agregar a la Receta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
