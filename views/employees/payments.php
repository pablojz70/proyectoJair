<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Registrar Pago a Empleado</div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/employees/doPayment">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Empleado</label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= h($e['name']) ?> (<?= h($e['username']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de pago</label>
                        <select name="payment_type" class="form-select">
                            <option value="comision">Comision</option>
                            <option value="bono">Bono</option>
                            <option value="salario">Salario</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto (USD)</label>
                        <input type="number" name="amount_usd" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periodo inicio</label>
                            <input type="date" name="period_start" class="form-control" value="<?= date('Y-m-01') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periodo fin</label>
                            <input type="date" name="period_end" class="form-control" value="<?= date('Y-m-t') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-2"></i>Registrar Pago</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-list me-2"></i>Historial de Pagos</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Empleado</th><th>Tipo</th><th>Monto</th><th>Periodo</th><th>Fecha</th></tr></thead>
                    <tbody>
                        <?php if (empty($allPayments)): ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">Sin pagos registrados</td></tr>
                        <?php else: ?>
                        <?php foreach ($allPayments as $pm): ?>
                        <tr>
                            <td><?= h($pm['employee_name']) ?></td>
                            <td><span class="badge bg-<?= $pm['payment_type'] === 'bono' ? 'success' : ($pm['payment_type'] === 'comision' ? 'info' : 'secondary') ?>"><?= ucfirst($pm['payment_type']) ?></span></td>
                            <td class="fw-bold">$<?= number_format($pm['amount_usd'], 2) ?></td>
                            <td><small><?= format_date($pm['period_start']) ?> - <?= format_date($pm['period_end']) ?></small></td>
                            <td><?= format_date($pm['paid_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
