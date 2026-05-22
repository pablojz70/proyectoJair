<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Venta #<?= $sale['id'] ?></span>
                <a href="<?= BASE_URL ?>/sales" class="btn btn-sm btn-secondary">Volver</a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <small class="text-muted">Cliente</small>
                        <p class="fw-bold"><?= h($sale['client_name']) ?></p>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Empleado</small>
                        <p><?= h($sale['employee_name'] ?? '-') ?></p>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Tipo</small>
                        <p><?= get_status_badge($sale['sale_type']) ?></p>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Estado</small>
                        <p><?= get_status_badge($sale['status']) ?></p>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted">Fecha</small>
                        <p><?= format_datetime($sale['created_at']) ?></p>
                    </div>
                    <?php if ($sale['due_date']): ?>
                    <div class="col-md-1">
                        <small class="text-muted">Vence</small>
                        <p><?= format_date($sale['due_date']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= h($item['product_name']) ?></td>
                            <td><span class="badge bg-<?= $item['product_type'] === 'simple' ? 'primary' : 'info' ?>"><?= $item['product_type'] ?></span></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= format_usd($item['unit_price_usd']) ?></td>
                            <td><?= format_usd($item['subtotal_usd']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total USD:</td>
                            <td class="fw-bold"><?= format_usd($sale['total_usd']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Tasa Bs/USD:</td>
                            <td><?= number_format($sale['exchange_rate'], 2) ?></td>
                        </tr>
                        <tr class="table-primary">
                            <td colspan="4" class="text-end fw-bold fs-5">Total Bs:</td>
                            <td class="fw-bold fs-5"><?= format_bs($sale['total_bs']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-credit-card me-2"></i>Pagos
            </div>
            <div class="card-body">
                <?php if (empty($payments)): ?>
                <p class="text-muted text-center">Sin pagos registrados</p>
                <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>USD</th>
                            <th>Bs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= format_date($payment['created_at']) ?></td>
                            <td><?= format_usd($payment['amount_usd']) ?></td>
                            <td><?= format_bs($payment['amount_bs']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Total pagado:</td>
                            <td><?= format_usd($totalPaid) ?></td>
                            <td><?= format_bs($totalPaid * $sale['exchange_rate']) ?></td>
                        </tr>
                        <?php if ($sale['status'] === 'pendiente'): ?>
                        <tr class="text-danger">
                            <td>Pendiente:</td>
                            <td><?= format_usd($sale['total_usd'] - $totalPaid) ?></td>
                            <td><?= format_bs(($sale['total_usd'] - $totalPaid) * $sale['exchange_rate']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
