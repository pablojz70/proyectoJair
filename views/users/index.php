<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Usuarios del Sistema</span>
        <a href="<?= BASE_URL ?>/users/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nuevo Usuario
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Telefono</th>
                        <th>Rol</th>
                        <th>Registrado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox me-2"></i>No hay usuarios registrados
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= h($u['name']) ?></td>
                        <td><strong><?= h($u['username']) ?></strong></td>
                        <td><?= h($u['email']) ?></td>
                        <td><?= h($u['phone'] ?? '-') ?></td>
                        <td><?= get_status_badge($u['role']) ?></td>
                        <td><?= format_date($u['created_at']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/users/edit/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary btn-icon" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ((int)$u['id'] !== (int)Session::get('user_id')): ?>
                            <a href="<?= BASE_URL ?>/users/delete/<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger btn-icon" title="Eliminar" onclick="return confirm('Eliminar a <?= h($u['name']) ?>?')">
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
