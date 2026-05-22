<?php $pageTitle = 'Pagina no encontrada'; ?>
<?php ob_start(); ?>
<div class="text-center py-5">
    <h1 class="display-1 text-muted">404</h1>
    <p class="lead">Pagina no encontrada</p>
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary">Ir al Dashboard</a>
</div>
<?php $content = ob_get_clean(); ?>
<?php if (!Session::isLoggedIn()): ?>
    <?= $content ?>
<?php else: ?>
    <?php require __DIR__ . '/layouts/header.php'; ?>
    <?= $content ?>
    <?php require __DIR__ . '/layouts/footer.php'; ?>
<?php endif; ?>
