<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-exclamation-triangle me-2"></i>Clientes Morosos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Deudas</th>
                        <th>Total Adeudado</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($overdueClients)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">
                            <i class="bi bi-check-circle me-2"></i>No hay clientes con deudas
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($overdueClients as $client): ?>
                    <tr class="<?= $client['overdue'] ? 'table-danger' : '' ?>">
                        <td><?= h($client['client_name']) ?></td>
                        <td><?= $client['count'] ?></td>
                        <td class="fw-bold"><?= format_usd($client['total_debt']) ?></td>
                        <td>
                            <?php if ($client['overdue']): ?>
                            <span class="badge bg-danger">Vencido</span>
                            <?php else: ?>
                            <span class="badge bg-warning text-dark">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                            <a href="<?= BASE_URL ?>/payments/client/<?= $client['client_id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-credit-card me-1"></i>Cobrar
                            </a>
                            <?php if (!empty($client['client_phone'])): ?>
                            <?php
                            $msg = "Hola " . $client['client_name'] . ", le recordamos que tiene un saldo pendiente de " . format_usd($client['total_debt']) . ". Por favor comuniquese para realizar el pago.";
                            $w = wa_link($client['client_phone'], $msg);
                            ?>
                            <a href="<?= $w ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Enviar WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-list me-2"></i>Todas las Deudas
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/payments" class="row g-2 mb-3">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Buscar cliente..." value="<?= h($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                <a href="<?= BASE_URL ?>/payments" class="btn btn-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th># Venta</th>
                        <th>Cliente</th>
                        <th>Fecha Venta</th>
                        <th>Total USD</th>
                        <th>Pagado</th>
                        <th>Saldo</th>
                        <th>Vencimiento</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($debts)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-3 text-muted">
                            <i class="bi bi-check-circle me-2"></i>No hay deudas pendientes
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($debts as $debt): ?>
                    <?php $balance = $debt['total_usd'] - $debt['paid']; ?>
                    <tr>
                        <td><?= $debt['sale_id'] ?></td>
                        <td><?= h($debt['client_name']) ?></td>
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
                        <td>
                            <div class="d-flex gap-1">
                            <a href="<?= BASE_URL ?>/payments/client/<?= $debt['client_id'] ?>" class="btn btn-sm btn-outline-primary btn-icon">
                                <i class="bi bi-credit-card"></i>
                            </a>
                            <?php if (!empty($debt['client_phone'])): ?>
                            <?php
                            $msg = "Hola " . $debt['client_name'] . ", recuerde que tiene una venta a credito de " . format_usd($debt['total_usd']) . " (saldo: " . format_usd($balance) . ") vencida el " . format_date($debt['due_date']) . ". Por favor pongase al dia.";
                            $w = wa_link($debt['client_phone'], $msg);
                            ?>
                            <a href="<?= $w ?>" target="_blank" class="btn btn-sm btn-outline-success btn-icon" title="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
