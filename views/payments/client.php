<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-credit-card me-2"></i>Deudas de <?= h($client['full_name']) ?></span>
                <a href="<?= BASE_URL ?>/payments" class="btn btn-sm btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <?php if (empty($debts)): ?>
                <p class="text-muted text-center py-3">
                    <i class="bi bi-check-circle me-2"></i>Este cliente no tiene deudas pendientes
                </p>
                <?php else: ?>
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong>Deuda total:</strong> <?= format_usd($totalDebt) ?>
                        (<?= format_bs($totalDebt * ($exchangeRate ?: 1)) ?> a tasa Bs. <?= number_format($exchangeRate ?: 0, 2) ?>)
                    </div>
                    <?php if (!empty($client['phone'])): ?>
                    <?php
                    $msg = "Hola " . $client['full_name'] . ", le notificamos que tiene una deuda pendiente de " . format_usd($totalDebt) . ". Por favor contactenos para ponerse al dia. Gracias.";
                    $w = wa_link($client['phone'], $msg);
                    ?>
                    <a href="<?= $w ?>" target="_blank" class="btn btn-success btn-sm">
                        <i class="bi bi-whatsapp me-1"></i>Enviar Deuda por WhatsApp
                    </a>
                    <?php endif; ?>
                </div>

                <form method="POST" action="<?= BASE_URL ?>/payments/pay" id="paymentForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="client_id" value="<?= $client['id'] ?>">
                    <input type="hidden" name="exchange_rate" value="<?= $exchangeRate ?>">

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th># Venta</th>
                                <th>Fecha</th>
                                <th>Total USD</th>
                                <th>Pagado</th>
                                <th>Saldo</th>
                                <th>Vence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($debts as $debt): ?>
                            <?php $balance = $debt['total_usd'] - $debt['paid']; ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="sale_ids[]" value="<?= $debt['sale_id'] ?>" class="sale-checkbox" data-balance="<?= $balance ?>">
                                </td>
                                <td><?= $debt['sale_id'] ?></td>
                                <td><?= format_date($debt['sale_date']) ?></td>
                                <td><?= format_usd($debt['total_usd']) ?></td>
                                <td><?= format_usd($debt['paid']) ?></td>
                                <td class="fw-bold"><?= format_usd($balance) ?></td>
                                <td>
                                    <?php if ($debt['due_date']): ?>
                                        <?= format_date($debt['due_date']) ?>
                                        <?php if (strtotime($debt['due_date']) < time()): ?>
                                            <span class="badge bg-danger">Vencido</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monto a pagar (USD)</label>
                            <input type="number" name="amount_usd" class="form-control" id="amountUsd" step="0.01" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Monto a pagar (Bs)</label>
                            <input type="number" name="amount_bs" class="form-control" id="amountBs" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Registrar Pago
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Historial de Pagos
            </div>
            <div class="card-body">
                <?php if (empty($payments)): ?>
                <p class="text-muted text-center py-3">Sin pagos registrados</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Venta</th>
                            <th>USD</th>
                            <th>Bs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= format_date($payment['created_at']) ?></td>
                            <td>#<?= $payment['sale_id'] ?></td>
                            <td><?= format_usd($payment['amount_usd']) ?></td>
                            <td><?= format_bs($payment['amount_bs']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.sale-checkbox').forEach(cb => cb.checked = this.checked);
        updateTotalSelected();
    });

    document.querySelectorAll('.sale-checkbox').forEach(cb => {
        cb.addEventListener('change', updateTotalSelected);
    });

    function updateTotalSelected() {
        let total = 0;
        document.querySelectorAll('.sale-checkbox:checked').forEach(cb => {
            total += parseFloat(cb.dataset.balance) || 0;
        });
        const usdInput = document.getElementById('amountUsd');
        if (usdInput && !usdInput.value) {
            usdInput.value = total.toFixed(2);
        }
    }

    const rate = <?= $exchangeRate ?: 0 ?>;
    const usdInput = document.getElementById('amountUsd');
    const bsInput = document.getElementById('amountBs');

    if (usdInput && bsInput) {
        usdInput.addEventListener('input', function() {
            if (rate > 0) {
                bsInput.value = (parseFloat(this.value) * rate).toFixed(2);
            }
        });

        bsInput.addEventListener('input', function() {
            if (rate > 0) {
                usdInput.value = (parseFloat(this.value) / rate).toFixed(2);
            }
        });
    }
});
</script>
