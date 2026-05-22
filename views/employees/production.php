<div class="card">
    <div class="card-header"><i class="bi bi-gear me-2"></i>Registrar Produccion</div>
    <div class="card-body">
        <?= flash_messages() ?>
        <form method="POST" action="<?= BASE_URL ?>/employees/doProduction">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Empleado</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($employees as $e): ?>
                        <?php if ($e['employee_status'] === 'activo'): ?>
                        <option value="<?= $e['id'] ?>" <?= Session::get('user_id') == $e['id'] ? 'selected' : '' ?>><?= h($e['name']) ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Producto Compuesto</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($compoundProducts as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= h($p['name']) ?> - Costo: <?= format_usd($p['production_cost_usd']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="produced_at" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-10">
                    <label class="form-label">Notas</label>
                    <input type="text" name="notes" class="form-control" placeholder="Opcional">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle me-2"></i>Producir</button>
                </div>
            </div>
        </form>
        <div class="alert alert-info mt-3 mb-0 py-2 small">
            <i class="bi bi-info-circle me-1"></i> Al producir se descuenta automaticamente el stock de materias primas.
            Cada 10 unidades acumuladas generan un bono.
        </div>
    </div>
</div>
