<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-gear me-2"></i>Configuracion de Empleados</div>
            <div class="card-body">
                <p class="text-muted small">Estos valores aplican a <strong>todos</strong> los empleados por igual.</p>
                <?= flash_messages() ?>
                <form method="POST" action="<?= BASE_URL ?>/employees/saveSettings">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Comision sobre ventas (%)</label>
                        <div class="input-group">
                            <input type="number" name="commission_rate" class="form-control" value="<?= $settings['commission_rate'] ?>" step="0.01" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Porcentaje de comision que gana el empleado por cada venta que realiza.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bono por produccion ($)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="bonus_amount" class="form-control" value="<?= $settings['bonus_amount'] ?>" step="0.01" min="0" required>
                        </div>
                        <small class="text-muted">Monto en dolares que recibe el empleado cada vez que alcanza la meta de unidades.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cada cuantas unidades producidas se paga el bono</label>
                        <div class="input-group">
                            <input type="number" name="bonus_every_units" class="form-control" value="<?= $settings['bonus_every_units'] ?>" min="1" required>
                            <span class="input-group-text">unidades</span>
                        </div>
                        <small class="text-muted">Ej: si pones 10, el empleado recibe el bono cada 10 unidades producidas. Si pones 5, cada 5 unidades.</small>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Guardar Configuracion</button>
                </form>
            </div>
        </div>
    </div>
</div>
