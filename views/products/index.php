<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="bi bi-box me-2"></i>Productos</span>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/products/create?type=simple" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Simple
            </a>
            <a href="<?= BASE_URL ?>/products/create?type=compuesto" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg"></i> Compuesto
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/products" class="row g-2 mb-3">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Buscar producto..." value="<?= h($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <select name="type" class="form-select">
                    <option value="">Todos</option>
                    <option value="simple" <?= ($_GET['type'] ?? '') === 'simple' ? 'selected' : '' ?>>Simples</option>
                    <option value="compuesto" <?= ($_GET['type'] ?? '') === 'compuesto' ? 'selected' : '' ?>>Compuestos</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                <a href="<?= BASE_URL ?>/products" class="btn btn-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Stock</th>
                        <th>Precio Venta</th>
                        <th>Costo Prod.</th>
                        <th>Rinde</th>
                        <th>Margen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>No hay productos registrados
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <?php
                    $margin = 0;
                    $marginClass = '';
                    if ($product['type'] === 'compuesto' && $product['production_cost_usd'] > 0) {
                        $margin = (($product['sale_price_usd'] - $product['production_cost_usd']) / $product['sale_price_usd']) * 100;
                        $marginClass = $margin < 10 ? 'text-danger' : ($margin < 25 ? 'text-warning' : 'text-success');
                    }
                    ?>
                    <tr>
                        <td><?= h($product['name']) ?></td>
                        <td><?= get_status_badge($product['type']) ?></td>
                        <td>
                            <?php if ($product['type'] === 'simple'): ?>
                                <?= $product['stock'] ?>
                                <?php if ((float)$product['stock'] <= 0): ?>
                                    <span class="badge bg-danger">Sin stock</span>
                                <?php elseif ((float)$product['stock'] <= 5): ?>
                                    <span class="badge bg-warning text-dark">Poco stock</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-info text-dark">Virtual</span>
                            <?php endif; ?>
                        </td>
                        <td><?= format_usd($product['sale_price_usd']) ?></td>
                        <td>
                            <?php if ($product['production_cost_usd'] > 0): ?>
                                <?= format_usd($product['production_cost_usd']) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['type'] === 'compuesto'): ?>
                                <?= (int) ($product['recipe_yield'] ?? 1) ?> unid.
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="<?= $marginClass ?> fw-bold">
                            <?= $product['production_cost_usd'] > 0 ? number_format($margin, 1) . '%' : '-' ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($product['type'] === 'compuesto'): ?>
                            <a href="<?= BASE_URL ?>/products/recipe/<?= $product['id'] ?>" class="btn btn-sm btn-outline-info btn-icon" title="Receta">
                                <i class="bi bi-journal-text"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/products/delete/<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger btn-icon" title="Eliminar" onclick="return confirm('Eliminar <?= h($product['name']) ?>?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
