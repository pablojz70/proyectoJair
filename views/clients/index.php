<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Clientes</span>
        <div>
            <a href="<?= BASE_URL ?>/clients/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo Cliente
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>/clients" class="row g-2 mb-3">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o cedula..." value="<?= h($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                <a href="<?= BASE_URL ?>/clients" class="btn btn-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cedula/RIF</th>
                        <th>Telefono</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>No hay clientes registrados
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= h($client['full_name']) ?></td>
                        <td><?= h($client['cedula_rif']) ?></td>
                        <td><?= h($client['phone']) ?></td>
                        <td class="text-muted small"><?= h(substr($client['notes'] ?? '', 0, 50)) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/clients/edit/<?= $client['id'] ?>" class="btn btn-sm btn-outline-primary btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/clients/history/<?= $client['id'] ?>" class="btn btn-sm btn-outline-info btn-icon" title="Historial">
                                <i class="bi bi-clock-history"></i>
                            </a>
                            <?php if (Session::isAdmin()): ?>
                            <a href="<?= BASE_URL ?>/clients/delete/<?= $client['id'] ?>" class="btn btn-sm btn-outline-danger btn-icon" title="Eliminar" onclick="return confirm('Eliminar este cliente?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
