<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-graph-up me-2"></i>Rentabilidad</span>
        <div>
            <a href="<?= BASE_URL ?>/reports" class="btn btn-sm btn-outline-primary">Resumen</a>
            <a href="<?= BASE_URL ?>/reports/inventory" class="btn btn-sm btn-outline-primary">Inventario</a>
            <a href="<?= BASE_URL ?>/reports/collections" class="btn btn-sm btn-outline-primary">Cobranzas</a>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Margen de ganancia promedio:</strong>
            <span class="fs-5 fw-bold"><?= number_format($avgMargin, 1) ?>%</span>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fw-bold">Top 10 Productos mas Vendidos</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cant. Vendida</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topSold as $i => $ps): ?>
                        <?php if ($ps['qty'] <= 0) continue; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= h($ps['name']) ?></td>
                            <td><span class="badge bg-<?= $ps['type'] === 'simple' ? 'primary' : 'info' ?>"><?= $ps['type'] ?></span></td>
                            <td><?= $ps['qty'] ?></td>
                            <td><?= format_usd($ps['revenue']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Top 10 Productos mas Rentables</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Ganancia</th>
                            <th>Margen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProfitable as $i => $ps): ?>
                        <?php if ($ps['profit'] <= 0) continue; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= h($ps['name']) ?></td>
                            <td class="fw-bold text-success"><?= format_usd($ps['profit']) ?></td>
                            <td><?= number_format($ps['margin'], 1) ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
