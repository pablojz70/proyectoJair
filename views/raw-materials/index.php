<?php if (!empty($lowStock)): ?>
<div class="alert alert-warning alert-dismissible fade show">
    <strong><i class="bi bi-exclamation-triangle me-2"></i>Stock Bajo!</strong>
    <?php foreach ($lowStock as $mat): ?>
    <span class="badge bg-warning text-dark me-1"><?= h($mat['name']) ?> (<?= $mat['stock'] ?> <?= $mat['unit'] ?>)</span>
    <?php endforeach; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-boxes me-2"></i>Materias Primas</span>
        <a href="<?= BASE_URL ?>/raw-materials/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/raw-materials" class="row g-2 mb-3">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Buscar materia prima..." value="<?= h($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                <a href="<?= BASE_URL ?>/raw-materials" class="btn btn-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Unidad</th>
                        <th>Stock</th>
                        <th>Costo Unit. (USD)</th>
                        <th>Stock Minimo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($materials)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>No hay materias primas registradas
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($materials as $mat): ?>
                    <?php $isLow = $mat['stock'] <= $mat['min_stock']; ?>
                    <tr class="<?= $isLow ? 'table-warning' : '' ?>">
                        <td><?= h($mat['name']) ?></td>
                        <td><?= h($mat['unit']) ?></td>
                        <td class="<?= $isLow ? 'fw-bold text-danger' : '' ?>">
                            <?= $mat['stock'] ?> <?= h($mat['unit']) ?>
                        </td>
                        <td><?= format_usd($mat['unit_cost_usd']) ?></td>
                        <td><?= $mat['min_stock'] ?></td>
                        <td>
                            <?php if ($isLow): ?>
                            <span class="badge bg-danger">Bajo</span>
                            <?php else: ?>
                            <span class="badge bg-success">OK</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/raw-materials/edit/<?= $mat['id'] ?>" class="btn btn-sm btn-outline-primary btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-success btn-icon" title="Ajustar Stock"
                                data-bs-toggle="modal" data-bs-target="#stockModal" data-id="<?= $mat['id'] ?>" data-name="<?= h($mat['name']) ?>"
                                data-pqty="<?= $mat['presentation_qty'] ?>" data-unit="<?= h($mat['unit']) ?>">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                            <a href="<?= BASE_URL ?>/raw-materials/delete/<?= $mat['id'] ?>" class="btn btn-sm btn-outline-danger btn-icon" title="Eliminar" onclick="return confirm('Eliminar <?= h($mat['name']) ?>?')">
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

<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Ajustar Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Agregar stock a: <strong id="modalMaterialName"></strong></p>
                    <p class="text-muted small">Cada presentacion = <strong id="modalPqty">1</strong> <span id="modalUnit">unidad</span></p>
                    <div class="mb-3">
                        <label class="form-label">Cantidad de presentaciones a agregar</label>
                        <input type="number" name="packages" class="form-control" id="packagesInput" min="1" step="1" required>
                    </div>
                    <div class="alert alert-info py-2">
                        Total a agregar: <strong id="totalToAdd">0</strong> <span id="totalUnit">unidad</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>Agregar Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('stockModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-id');
    var name = button.getAttribute('data-name');
    var pqty = parseFloat(button.getAttribute('data-pqty')) || 1;
    var unit = button.getAttribute('data-unit') || 'unidad';
    document.getElementById('modalMaterialName').textContent = name;
    document.getElementById('modalPqty').textContent = pqty;
    document.getElementById('modalUnit').textContent = unit;
    document.getElementById('totalUnit').textContent = unit;
    this.querySelector('form').action = '<?= BASE_URL ?>/raw-materials/adjust/' + id;
    updateTotal();
});

document.getElementById('packagesInput').addEventListener('input', updateTotal);

function updateTotal() {
    var pqty = parseFloat(document.getElementById('modalPqty').textContent) || 1;
    var pkgs = parseFloat(document.getElementById('packagesInput').value) || 0;
    document.getElementById('totalToAdd').textContent = (pqty * pkgs).toFixed(2);
}
</script>
