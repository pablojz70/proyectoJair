<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-boxes me-2"></i>Inventario</span>
        <div>
            <a href="<?= BASE_URL ?>/reports" class="btn btn-sm btn-outline-primary">Resumen</a>
            <a href="<?= BASE_URL ?>/reports/profitability" class="btn btn-sm btn-outline-primary">Rentabilidad</a>
            <a href="<?= BASE_URL ?>/reports/collections" class="btn btn-sm btn-outline-primary">Cobranzas</a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <input type="hidden" name="action" value="inventory">
            <div class="col-auto">
                <label class="form-label">Alerta stock menor a:</label>
            </div>
            <div class="col-auto">
                <input type="number" name="threshold" class="form-control" value="<?= (int) ($_GET['threshold'] ?? 10) ?>" min="0">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary mt-1">Filtrar</button>
            </div>
        </form>

        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fw-bold">Materias Primas <span class="badge bg-secondary"><?= count($materials) ?></span></h6>
                <div class="table-responsive" style="max-height: 400px">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Stock</th>
                                <th>Min</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $m): ?>
                            <?php $isLow = $m['stock'] <= $m['min_stock']; ?>
                            <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                                <td><?= h($m['name']) ?></td>
                                <td><?= $m['stock'] ?> <?= h($m['unit']) ?></td>
                                <td><?= $m['min_stock'] ?></td>
                                <td>
                                    <?php if ($isLow): ?>
                                    <span class="badge bg-danger">BAJO</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">OK</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Productos Simples <span class="badge bg-secondary"><?= count($products) ?></span></h6>
                <div class="table-responsive" style="max-height: 400px">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Stock</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                            <?php $isLow = $p['stock'] <= $threshold; ?>
                            <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                                <td><?= h($p['name']) ?></td>
                                <td><?= $p['stock'] ?></td>
                                <td>
                                    <?php if ((float)$p['stock'] <= 0): ?>
                                    <span class="badge bg-danger">SIN STOCK</span>
                                    <?php elseif ($isLow): ?>
                                    <span class="badge bg-warning text-dark">BAJO</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">OK</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($nonProducible)): ?>
        <hr>
        <h6 class="fw-bold text-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>Productos Compuestos que NO se pueden producir
        </h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Materia Prima Faltante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nonProducible as $np): ?>
                    <tr>
                        <td class="fw-bold"><?= h($np['product']) ?></td>
                        <td>
                            <?php foreach ($np['missing'] as $m): ?>
                            <span class="badge bg-danger me-1">
                                <?= h($m['name']) ?> (necesita <?= $m['needed'] ?> <?= $m['unit'] ?>, tiene <?= $m['available'] ?>)
                            </span>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
