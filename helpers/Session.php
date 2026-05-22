<?php
class Session
{
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public static function destroy()
    {
        session_destroy();
    }

    public static function isLoggedIn()
    {
        return self::has('user_id');
    }

    public static function isAdmin()
    {
        return self::get('user_role') === 'admin';
    }

    public static function requireLogin()
    {
        self::init();
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function requireAdmin()
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    }

    public static function setFlash($key, $message)
    {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash($key)
    {
        $message = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }

    public static function hasFlash($key)
    {
        return isset($_SESSION['_flash'][$key]);
    }
}
