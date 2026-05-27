<div class="card">
    <div class="card-header d-none d-md-block">
        <i class="bi bi-cart-plus me-2"></i>Nueva Venta
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/sales/store" id="saleForm">
            <?= csrf_field() ?>

            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Cliente *</label>
                    <div class="input-group">
                        <select name="client_id" class="form-select" id="clientSelect" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= h($client['full_name']) ?> - <?= h($client['cedula_rif']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-success" type="button" title="Nuevo Cliente" data-bs-toggle="modal" data-bs-target="#newClientModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo de venta</label>
                    <select name="sale_type" class="form-select" id="saleType">
                        <option value="contado">Contado</option>
                        <option value="credito">Credito</option>
                    </select>
                </div>
                <div class="col-md-2" id="dueDateField" style="display:none">
                    <label class="form-label">Fecha vencimiento</label>
                    <input type="date" name="due_date" class="form-control" id="dueDate">
                </div>
                <div class="col-12 d-md-none mb-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#extraSaleFields" aria-expanded="false">
                        <i class="bi bi-three-dots me-1"></i>Opciones avanzadas (Tasa, Vendedor)
                    </button>
                </div>
                <div class="collapse d-md-flex" id="extraSaleFields">
                    <div class="col-md-3 pe-md-2">
                        <label class="form-label">Tasa Bs/USD</label>
                        <div class="input-group">
                            <input type="number" name="exchange_rate" class="form-control" id="exchangeRate" value="<?= $exchangeRate ?: '' ?>" step="0.01" readonly>
                            <button type="button" class="btn btn-outline-secondary" title="Editar tasa" id="editRateBtn">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                        <input type="number" name="manual_rate" class="form-control mt-1" id="manualRate" style="display:none" placeholder="Tasa manual" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Vendedor / Empleado</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($employees as $emp): ?>
                            <?php
                            $isActive = ($emp['role'] === 'empleado' && ($emp['employee_status'] ?? 'activo') === 'activo') || $emp['role'] === 'vendedor';
                            $isMe = (int)Session::get('user_id') === (int)$emp['id'];
                            ?>
                            <?php if ($isActive || $isMe): ?>
                            <option value="<?= $emp['id'] ?>" <?= $isMe ? 'selected' : '' ?>>
                                <?= h($emp['name']) ?> (<?= ucfirst($emp['role']) ?>)
                            </option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="fw-bold"><i class="bi bi-box me-2"></i>Productos</h6>
            <div id="productContainer">
                <div class="row g-2 mb-2 product-row">
                    <div class="col-md-4">
                        <select name="product_id[]" class="form-select product-select" required>
                            <option value="">Seleccionar producto...</option>
                            <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>"
                                data-price="<?= $product['sale_price_usd'] ?>"
                                data-cost="<?= $product['production_cost_usd'] ?: 0 ?>"
                                data-type="<?= $product['type'] ?>"
                                data-stock="<?= $product['stock'] ?>">
                                <?= h($product['name']) ?>
                                <?php if ($product['type'] === 'compuesto' && $product['recipe_yield'] > 0): ?>
                                    (c/u, rinde <?= (int)$product['recipe_yield'] ?>)
                                <?php endif; ?>
                                - <?= format_usd($product['sale_price_usd']) ?> c/u
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-5">
                        <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="Cantidad" min="1" step="1" required>
                    </div>
                    <div class="col-md-2 d-none d-md-block">
                        <input type="text" class="form-control unit-price" placeholder="Precio USD" readonly>
                    </div>
                    <div class="col-md-1 d-none d-md-block">
                        <input type="text" class="form-control unit-cost" placeholder="Costo" readonly style="font-size:0.8rem;color:#6c757d">
                    </div>
                    <div class="col-md-2 d-none d-md-block">
                        <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                    </div>
                    <div class="col-md-1 col-3 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-info btn-sm w-100 toggle-detail d-md-none" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-product" disabled>
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="col-12 d-none mobile-detail">
                        <div class="row g-1 mt-1">
                            <div class="col-4"><input type="text" class="form-control form-control-sm unit-price" placeholder="Precio USD" readonly></div>
                            <div class="col-3"><input type="text" class="form-control form-control-sm unit-cost" placeholder="Costo" readonly style="font-size:0.8rem;color:#6c757d"></div>
                            <div class="col-5"><input type="text" class="form-control form-control-sm subtotal" placeholder="Subtotal" readonly></div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-success mt-2" id="addProduct">
                <i class="bi bi-plus-lg"></i> Agregar producto
            </button>

            <hr>
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">Subtotal USD:</td>
                            <td class="text-end" id="subtotalDisplay">$ 0.00</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tasa Bs/USD:</td>
                            <td class="text-end" id="rateDisplay"><?= $exchangeRate ? number_format($exchangeRate, 2) : 'N/A' ?></td>
                        </tr>
                        <tr class="table-active">
                            <td class="fw-bold fs-5">Total Bs:</td>
                            <td class="text-end fs-5 fw-bold" id="totalBsDisplay">Bs. 0.00</td>
                        </tr>
                        <tr class="table-primary">
                            <td class="fw-bold fs-5">Total USD:</td>
                            <td class="text-end fs-5 fw-bold" id="totalUsdDisplay">$ 0.00</td>
                        </tr>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-check-circle me-2"></i>Confirmar Venta
            </button>
            <a href="<?= BASE_URL ?>/sales" class="btn btn-secondary btn-lg">Cancelar</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dueDate = document.getElementById('dueDate');
    const saleType = document.getElementById('saleType');
    const dueDateField = document.getElementById('dueDateField');

    function getLastDayOfMonth() {
        var d = new Date();
        var last = new Date(d.getFullYear(), d.getMonth() + 1, 0);
        return last.toISOString().split('T')[0];
    }

    saleType.addEventListener('change', function() {
        dueDateField.style.display = this.value === 'credito' ? 'block' : 'none';
        if (this.value === 'credito') {
            document.querySelector('[name="due_date"]').required = true;
            if (!dueDate.value) {
                dueDate.value = getLastDayOfMonth();
            }
        } else {
            document.querySelector('[name="due_date"]').required = false;
        }
    });

    const editRateBtn = document.getElementById('editRateBtn');
    const exchangeRate = document.getElementById('exchangeRate');
    const manualRate = document.getElementById('manualRate');

    editRateBtn.addEventListener('click', function() {
        manualRate.style.display = 'block';
        exchangeRate.readOnly = false;
        exchangeRate.focus();
    });

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.product-row').forEach(function(row) {
            const sub = row.querySelector('.subtotal');
            const val = parseFloat(sub.value.replace(/[^0-9.-]/g, '')) || 0;
            subtotal += val;
        });

        const rate = parseFloat(exchangeRate.value) || 0;
        const totalBs = subtotal * rate;

        document.getElementById('subtotalDisplay').textContent = '$ ' + subtotal.toFixed(2);
        document.getElementById('totalUsdDisplay').textContent = '$ ' + subtotal.toFixed(2);
        document.getElementById('totalBsDisplay').textContent = 'Bs. ' + totalBs.toFixed(2);
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('.product-row');
            updateRowSubtotal(row);
            updateTotals();
        }

        if (e.target.classList.contains('quantity-input')) {
            const row = e.target.closest('.product-row');
            updateRowSubtotal(row);
            updateTotals();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const row = e.target.closest('.product-row');
            updateRowSubtotal(row);
            updateTotals();
        }
        if (e.target.id === 'exchangeRate' || e.target.id === 'manualRate') {
            updateTotals();
        }
    });

    function updateRowSubtotal(row) {
        const select = row.querySelector('.product-select');
        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
        const cost = parseFloat(select.selectedOptions[0]?.dataset.cost || 0);
        const subtotal = qty * price;
        row.querySelectorAll('.unit-price').forEach(function(el) { el.value = '$ ' + price.toFixed(2); });
        row.querySelectorAll('.unit-cost').forEach(function(el) { el.value = '$ ' + cost.toFixed(2); });
        row.querySelectorAll('.subtotal').forEach(function(el) { el.value = '$ ' + subtotal.toFixed(2); });
    }

    document.getElementById('addProduct').addEventListener('click', function() {
        const container = document.getElementById('productContainer');
        const template = container.querySelector('.product-row');
        const clone = template.cloneNode(true);
        clone.querySelectorAll('select, input').forEach(el => el.value = '');
        clone.querySelector('.remove-product').disabled = false;
        container.appendChild(clone);
        updateRemoveButtons();
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-detail')) {
            var row = e.target.closest('.product-row');
            var detail = row.querySelector('.mobile-detail');
            if (detail) detail.classList.toggle('d-none');
        }
        if (e.target.closest('.remove-product')) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                e.target.closest('.product-row').remove();
                updateRemoveButtons();
                updateTotals();
            }
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(function(row, index) {
            row.querySelector('.remove-product').disabled = rows.length <= 1;
        });
    }
});
</script>

<div class="modal fade" id="newClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickClientForm">
                <div class="modal-body">
                    <div id="quickClientMsg"></div>
                    <div class="mb-3">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cedula / RIF *</label>
                        <input type="text" name="cedula_rif" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefono</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="notes" class="form-control" rows="1"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="saveClientBtn">
                        <i class="bi bi-save me-2"></i>Guardar y Seleccionar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('quickClientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = document.getElementById('saveClientBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    var formData = new FormData(this);
    formData.append('ajax', '1');

    fetch('<?= BASE_URL ?>/clients/quickStore', {
        method: 'POST',
        body: new URLSearchParams(formData),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var opt = document.createElement('option');
            opt.value = data.id;
            opt.text = data.full_name + ' - ' + data.cedula_rif;
            opt.selected = true;
            document.getElementById('clientSelect').appendChild(opt);
            var modal = bootstrap.Modal.getInstance(document.getElementById('newClientModal'));
            modal.hide();
            document.getElementById('quickClientForm').reset();
            document.getElementById('quickClientMsg').innerHTML = '';
        } else {
            document.getElementById('quickClientMsg').innerHTML = '<div class="alert alert-danger py-2">' + data.error + '</div>';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-2"></i>Guardar y Seleccionar';
    })
    .catch(function() {
        document.getElementById('quickClientMsg').innerHTML = '<div class="alert alert-danger py-2">Error de conexion</div>';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-2"></i>Guardar y Seleccionar';
    });
});
</script>
