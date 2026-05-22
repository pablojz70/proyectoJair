<?php
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function old($field, $default = '')
{
    return $_POST[$field] ?? $default;
}

function csrf_token()
{
    if (!Session::has('csrf_token')) {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
    }
    return Session::get('csrf_token');
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(Session::get('csrf_token', ''), $token) || empty($token)) {
        die('Token de seguridad invalido');
    }
}

function h($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function format_usd($amount)
{
    return '$ ' . number_format((float) $amount, 2);
}

function format_bs($amount)
{
    return 'Bs. ' . number_format((float) $amount, 2);
}

function format_date($date)
{
    return date('d/m/Y', strtotime($date));
}

function format_datetime($datetime)
{
    return date('d/m/Y h:i A', strtotime($datetime));
}

function alert_success($message)
{
    Session::setFlash('success', $message);
}

function alert_error($message)
{
    Session::setFlash('error', $message);
}

function flash_messages()
{
    $types = ['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'];
    $html = '';
    foreach ($types as $key => $class) {
        if (Session::hasFlash($key)) {
            $msg = h(Session::getFlash($key));
            $html .= '<div class="alert alert-' . $class . ' alert-dismissible fade show" role="alert">'
                . $msg
                . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
                . '</div>';
        }
    }
    return $html;
}

function is_ajax()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function json_response($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function get_status_badge($status)
{
    $map = [
        'pagada' => 'success',
        'pendiente' => 'warning',
        'cancelada' => 'danger',
        'contado' => 'primary',
        'credito' => 'info',
        'admin' => 'danger',
        'vendedor' => 'primary',
        'empleado' => 'success',
    ];
    $class = $map[$status] ?? 'secondary';
    return '<span class="badge bg-' . $class . '">' . h($status) . '</span>';
}
