<?php
require_once __DIR__ . '/helpers/Session.php';
Session::init();
$base = defined('BASE_URL') ? BASE_URL : '';
header('Content-Type: application/json');
echo json_encode([
    'name' => 'Sistema de Ventas',
    'short_name' => 'Ventas',
    'description' => 'Sistema de ventas con gestion de recetas',
    'start_url' => $base . '/login',
    'display' => 'standalone',
    'background_color' => '#f8f9fa',
    'theme_color' => '#a8d8ea',
    'orientation' => 'portrait',
    'icons' => [
        [
            'src' => $base . '/imagen/ventas..png',
            'sizes' => '48x48',
            'type' => 'image/png',
        ],
        [
            'src' => $base . '/imagen/ventas..png',
            'sizes' => '192x192',
            'type' => 'image/png',
        ],
    ],
]);
