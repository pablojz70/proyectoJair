<?php
class ClientController
{
    private $clientModel;

    public function __construct()
    {
        Session::requireLogin();
        $this->clientModel = new Client();
    }

    public function index()
    {
        $pageTitle = 'Clientes';
        $search = $_GET['search'] ?? '';
        $userId = Session::get('user_id');
        $clients = $this->clientModel->getAll($userId, $search);

        ob_start();
        require __DIR__ . '/../views/clients/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function create()
    {
        $pageTitle = 'Nuevo Cliente';
        ob_start();
        require __DIR__ . '/../views/clients/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/clients');
        }

        $data = [
            'user_id' => Session::get('user_id'),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'cedula_rif' => trim($_POST['cedula_rif'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        if (empty($data['full_name'])) {
            alert_error('El nombre completo es requerido');
            redirect(BASE_URL . '/clients/create');
        }

        if (empty($data['cedula_rif'])) {
            alert_error('La cedula/RIF es requerida');
            redirect(BASE_URL . '/clients/create');
        }

        if ($this->clientModel->existsByCedula($data['cedula_rif'], $data['user_id'])) {
            alert_error('Ya existe un cliente con esa cedula/RIF');
            redirect(BASE_URL . '/clients/create');
        }

        $id = $this->clientModel->create($data);
        alert_success('Cliente creado exitosamente');
        redirect(BASE_URL . '/clients');
    }

    public function quickStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_response(['success' => false, 'error' => 'Metodo no permitido'], 405);
        }

        $data = [
            'user_id' => Session::get('user_id'),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'cedula_rif' => trim($_POST['cedula_rif'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        $errors = [];
        if (empty($data['full_name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['cedula_rif'])) $errors[] = 'La cedula/RIF es requerida';
        if ($this->clientModel->existsByCedula($data['cedula_rif'], $data['user_id'])) {
            $errors[] = 'Ya existe un cliente con esa cedula/RIF';
        }

        if (!empty($errors)) {
            json_response(['success' => false, 'error' => implode('. ', $errors)]);
        }

        $id = $this->clientModel->create($data);
        json_response([
            'success' => true,
            'id' => $id,
            'full_name' => $data['full_name'],
            'cedula_rif' => $data['cedula_rif'],
        ]);
    }

    public function edit()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/clients');
        }

        $userId = Session::get('user_id');
        $client = $this->clientModel->findById($id, $userId);

        if (!$client) {
            alert_error('Cliente no encontrado');
            redirect(BASE_URL . '/clients');
        }

        $pageTitle = 'Editar Cliente';
        ob_start();
        require __DIR__ . '/../views/clients/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function update()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/clients');
        }

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'cedula_rif' => trim($_POST['cedula_rif'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        if (empty($data['full_name'])) {
            alert_error('El nombre completo es requerido');
            redirect(BASE_URL . "/clients/edit/{$id}");
        }

        if (empty($data['cedula_rif'])) {
            alert_error('La cedula/RIF es requerida');
            redirect(BASE_URL . "/clients/edit/{$id}");
        }

        $userId = Session::get('user_id');
        if ($this->clientModel->existsByCedula($data['cedula_rif'], $userId, $id)) {
            alert_error('Ya existe otro cliente con esa cedula/RIF');
            redirect(BASE_URL . "/clients/edit/{$id}");
        }

        $this->clientModel->update($id, $data);
        alert_success('Cliente actualizado exitosamente');
        redirect(BASE_URL . '/clients');
    }

    public function delete()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/clients');
        }

        $userId = Session::get('user_id');
        $this->clientModel->delete($id, $userId);
        alert_success('Cliente eliminado exitosamente');
        redirect(BASE_URL . '/clients');
    }

    public function history()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/clients');
        }

        $userId = Session::get('user_id');
        $client = $this->clientModel->findById($id, $userId);

        if (!$client) {
            alert_error('Cliente no encontrado');
            redirect(BASE_URL . '/clients');
        }

        $sales = $this->clientModel->getSalesHistory($id, $userId);
        $pageTitle = 'Historial de Ventas - ' . $client['full_name'];

        ob_start();
        require __DIR__ . '/../views/clients/history.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }
}
