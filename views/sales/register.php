<div class="card">
    <div class="card-header">
        <i class="bi bi-cart-plus me-2"></i>Nueva Venta
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/sales/store" id="saleForm">
            <?= csrf_field() ?>

            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Cliente *</label>
                    <select name="client_id" class="form-select" required>
                        <option value="">Seleccionar cliente...</option>
                        <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>"><?= h($client['full_name']) ?> - <?= h($client['cedula_rif']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Empleado</label>
                    <select name="employee_id" class="form-select">
                        <option value="">Ninguno</option>
                        <?php foreach ($employees as $emp): ?>
                        <?php if ($emp['status'] === 'activo'): ?>
                        <option value="<?= $emp['id'] ?>"><?= h($emp['name']) ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
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
                    <input type="date" name="due_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tasa Bs/USD</label>
                    <div class="input-group">
                        <input type="number" name="exchange_rate" class="form-control" id="exchangeRate" value="<?= $exchangeRate ?: '' ?>" step="0.01" readonly>
                        <button type="button" class="btn btn-outline-secondary" title="Editar tasa" id="editRateBtn">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    <input type="number" name="manual_rate" class="form-control mt-1" id="manualRate" style="display:none" placeholder="Tasa manual" step="0.01">
                </div>
            </div>

            <hr>
            <h6 class="fw-bold"><i class="bi bi-box me-2"></i>Productos</h6>
            <div id="productContainer">
                <div class="row g-2 mb-2 product-row">
                    <div class="col-md-5">
                        <select name="product_id[]" class="form-select product-select" required>
                            <option value="">Seleccionar producto...</option>
                            <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>"
                                data-price="<?= $product['sale_price_usd'] ?>"
                                data-type="<?= $product['type'] ?>"
                                data-stock="<?= $product['stock'] ?>">
                                <?= h($product['name']) ?>
                                (<?= $product['type'] ?>)
                                - <?= format_usd($product['sale_price_usd']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="quantity[]" class="form-control quantity-input" placeholder="Cantidad" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control unit-price" placeholder="Precio USD" readonly>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-product" disabled>
                            <i class="bi bi-x"></i>
                        </button>
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
    const saleType = document.getElementById('saleType');
    const dueDateField = document.getElementById('dueDateField');

    saleType.addEventListener('change', function() {
        dueDateField.style.display = this.value === 'credito' ? 'block' : 'none';
        if (this.value === 'credito') {
            document.querySelector('[name="due_date"]').required = true;
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
            const price = e.target.selectedOptions[0]?.dataset.price || 0;
            row.querySelector('.unit-price').value = '$ ' + parseFloat(price).toFixed(2);
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
        const subtotal = qty * price;
        row.querySelector('.subtotal').value = '$ ' + subtotal.toFixed(2);
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
