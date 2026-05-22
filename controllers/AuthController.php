<?php
class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        if (Session::isLoggedIn()) {
            redirect(BASE_URL . '/dashboard');
        }
        $pageTitle = 'Iniciar Sesion';
        ob_start();
        require __DIR__ . '/../views/auth/login.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function doLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/login');
        }

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login) || empty($password)) {
            alert_error('Usuario/correo y contrasena son requeridos');
            redirect(BASE_URL . '/login');
        }

        $user = $this->userModel->authenticate($login, $password);

        if (!$user) {
            alert_error('Usuario/correo o contrasena incorrectos');
            redirect(BASE_URL . '/login');
        }

        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_username', $user['username']);
        Session::set('user_email', $user['email']);
        Session::set('user_role', $user['role']);

        alert_success('Bienvenido de nuevo, ' . $user['name']);
        redirect(BASE_URL . '/dashboard');
    }

    public function register()
    {
        Session::requireAdmin();
        $pageTitle = 'Registrar Vendedor';
        ob_start();
        require __DIR__ . '/../views/auth/register.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function doRegister()
    {
        Session::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/auth/register');
        }

        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (empty($name)) $errors[] = 'El nombre es requerido';
        if (empty($username)) $errors[] = 'El nombre de usuario es requerido';
        if (empty($email)) $errors[] = 'El correo es requerido';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electronico invalido';
        if (strlen($password) < 6) $errors[] = 'La contrasena debe tener al menos 6 caracteres';
        if ($password !== $confirm) $errors[] = 'Las contrasenas no coinciden';

        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'Este correo ya esta registrado';
        }
        if ($this->userModel->existsByUsername($username)) {
            $errors[] = 'Este nombre de usuario ya esta en uso';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) alert_error($error);
            redirect(BASE_URL . '/auth/register');
        }

        $this->userModel->create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => 'vendedor',
        ]);

        alert_success('Vendedor registrado exitosamente');
        redirect(BASE_URL . '/auth/register');
    }

    public function profile()
    {
        Session::requireLogin();
        $user = $this->userModel->findById(Session::get('user_id'));

        if (!$user) {
            Session::destroy();
            alert_error('Sesion expirada, inicia sesion nuevamente');
            redirect(BASE_URL . '/login');
        }

        $pageTitle = 'Mi Perfil';
        ob_start();
        require __DIR__ . '/../views/auth/profile.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function doProfile()
    {
        Session::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/auth/profile');
        }

        $userId = Session::get('user_id');
        $user = $this->userModel->findById($userId);

        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (empty($name)) $errors[] = 'El nombre es requerido';
        if (empty($username)) $errors[] = 'El nombre de usuario es requerido';
        if (empty($email)) $errors[] = 'El correo es requerido';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electronico invalido';

        if ($email !== $user['email'] && $this->userModel->findByEmail($email)) {
            $errors[] = 'Este correo ya esta en uso por otro usuario';
        }
        if ($username !== $user['username'] && $this->userModel->existsByUsername($username)) {
            $errors[] = 'Este nombre de usuario ya esta en uso';
        }

        if (!empty($password)) {
            if (strlen($password) < 6) $errors[] = 'La contrasena debe tener al menos 6 caracteres';
            if ($password !== $confirm) $errors[] = 'Las contrasenas no coinciden';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) alert_error($error);
            redirect(BASE_URL . '/auth/profile');
        }

        $this->userModel->update($userId, [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'role' => $user['role'],
            'password' => $password,
        ]);

        Session::set('user_name', $name);
        Session::set('user_username', $username);
        Session::set('user_email', $email);

        alert_success('Perfil actualizado exitosamente');
        redirect(BASE_URL . '/auth/profile');
    }

    public function logout()
    {
        Session::destroy();
        redirect(BASE_URL . '/login');
    }
}
