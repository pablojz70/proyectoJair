<?php
class UserController
{
    private $model;

    public function __construct()
    {
        Session::requireAdmin();
        $this->model = new User();
    }

    public function index()
    {
        $pageTitle = 'Usuarios';
        $users = $this->model->getAll();

        ob_start();
        require __DIR__ . '/../views/users/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function create()
    {
        $pageTitle = 'Nuevo Usuario';
        ob_start();
        require __DIR__ . '/../views/users/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/users');
        }

        $role = $_POST['role'] ?? 'vendedor';
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $role,
            'commission_rate' => $role === 'empleado' ? (float) ($_POST['commission_rate'] ?? 0) : 0,
            'bonus_per_10_units' => $role === 'empleado' ? (float) ($_POST['bonus_per_10_units'] ?? 0) : 0,
        ];

        $errors = [];
        if (empty($data['name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['username'])) $errors[] = 'El nombre de usuario es requerido';
        if (empty($data['email'])) $errors[] = 'El correo es requerido';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electronico invalido';
        if (strlen($data['password']) < 6) $errors[] = 'La contrasena debe tener al menos 6 caracteres';

        if ($this->model->findByEmail($data['email'])) {
            $errors[] = 'Este correo ya esta registrado';
        }
        if ($this->model->existsByUsername($data['username'])) {
            $errors[] = 'Este nombre de usuario ya esta en uso';
        }

        if (!empty($errors)) {
            foreach ($errors as $e) alert_error($e);
            redirect(BASE_URL . '/users/create');
        }

        $this->model->create($data);
        alert_success('Usuario creado exitosamente');
        redirect(BASE_URL . '/users');
    }

    public function edit()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) redirect(BASE_URL . '/users');

        $user = $this->model->findById($id);
        if (!$user) {
            alert_error('Usuario no encontrado');
            redirect(BASE_URL . '/users');
        }

        $pageTitle = 'Editar Usuario';
        ob_start();
        require __DIR__ . '/../views/users/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function update()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/users');
        }

        $user = $this->model->findById($id);
        if (!$user) {
            alert_error('Usuario no encontrado');
            redirect(BASE_URL . '/users');
        }

        $role = $_POST['role'] ?? 'vendedor';
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $role,
            'commission_rate' => $role === 'empleado' ? (float) ($_POST['commission_rate'] ?? 0) : 0,
            'bonus_per_10_units' => $role === 'empleado' ? (float) ($_POST['bonus_per_10_units'] ?? 0) : 0,
            'employee_status' => $_POST['employee_status'] ?? 'activo',
        ];

        $errors = [];
        if (empty($data['name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['username'])) $errors[] = 'El nombre de usuario es requerido';
        if (empty($data['email'])) $errors[] = 'El correo es requerido';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electronico invalido';

        if ($data['email'] !== $user['email'] && $this->model->findByEmail($data['email'])) {
            $errors[] = 'Este correo ya esta en uso';
        }
        if ($data['username'] !== $user['username'] && $this->model->existsByUsername($data['username'])) {
            $errors[] = 'Este nombre de usuario ya esta en uso';
        }

        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'La contrasena debe tener al menos 6 caracteres';
        }

        if (!empty($errors)) {
            foreach ($errors as $e) alert_error($e);
            redirect(BASE_URL . "/users/edit/{$id}");
        }

        if (empty($data['password'])) {
            $data['password'] = '';
        }

        $this->model->update($id, $data);

        alert_success('Usuario actualizado exitosamente');
        redirect(BASE_URL . '/users');
    }

    public function delete()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) redirect(BASE_URL . '/users');

        if ((int)$id === (int)Session::get('user_id')) {
            alert_error('No puedes eliminarte a ti mismo');
            redirect(BASE_URL . '/users');
        }

        $this->model->delete($id);
        alert_success('Usuario eliminado exitosamente');
        redirect(BASE_URL . '/users');
    }
}
