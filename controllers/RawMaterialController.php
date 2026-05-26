<?php
class RawMaterialController
{
    private $model;

    public function __construct()
    {
        Session::requireLogin();
        $this->model = new RawMaterial();
    }

    public function index()
    {
        $pageTitle = 'Materias Primas';
        $search = $_GET['search'] ?? '';
        $userId = Session::get('user_id');
        $materials = $this->model->getAll($userId, $search);
        $lowStock = $this->model->getLowStock($userId);

        ob_start();
        require __DIR__ . '/../views/raw-materials/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function create()
    {
        $pageTitle = 'Nueva Materia Prima';
        $units = $this->model->getUnits();

        ob_start();
        require __DIR__ . '/../views/raw-materials/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/raw-materials');
        }

        $data = [
            'user_id' => Session::get('user_id'),
            'name' => trim($_POST['name'] ?? ''),
            'unit' => $_POST['unit'] ?? '',
            'presentation_qty' => (float) ($_POST['presentation_qty'] ?? 1),
            'stock' => (float) ($_POST['stock'] ?? 0),
            'unit_cost_usd' => (float) ($_POST['unit_cost_usd'] ?? 0),
            'min_stock' => (float) ($_POST['min_stock'] ?? 5),
        ];

        if (empty($data['name'])) {
            alert_error('El nombre es requerido');
            redirect(BASE_URL . '/raw-materials/create');
        }

        $this->model->create($data);
        alert_success('Materia prima creada exitosamente');
        redirect(BASE_URL . '/raw-materials');
    }

    public function edit()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/raw-materials');
        }

        $material = $this->model->findById($id);
        if (!$material) {
            alert_error('Materia prima no encontrada');
            redirect(BASE_URL . '/raw-materials');
        }

        $pageTitle = 'Editar Materia Prima';
        $units = $this->model->getUnits();

        ob_start();
        require __DIR__ . '/../views/raw-materials/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function update()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/raw-materials');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'unit' => $_POST['unit'] ?? '',
            'presentation_qty' => (float) ($_POST['presentation_qty'] ?? 1),
            'stock' => (float) ($_POST['stock'] ?? 0),
            'unit_cost_usd' => (float) ($_POST['unit_cost_usd'] ?? 0),
            'min_stock' => (float) ($_POST['min_stock'] ?? 5),
        ];

        $this->model->update($id, $data);
        alert_success('Materia prima actualizada exitosamente');
        redirect(BASE_URL . '/raw-materials');
    }

    public function adjust()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/raw-materials');
        }

        $material = $this->model->findById($id);
        $packages = (int) ($_POST['packages'] ?? 0);
        if ($packages <= 0) {
            alert_error('La cantidad de presentaciones debe ser mayor a 0');
            redirect(BASE_URL . '/raw-materials');
        }

        $presentationQty = (float) ($material['presentation_qty'] ?? 1);
        $quantity = $packages * $presentationQty;

        $this->model->adjustStock($id, $quantity);
        alert_success('Stock ajustado exitosamente');
        redirect(BASE_URL . '/raw-materials');
    }

    public function delete()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/raw-materials');
        }

        $this->model->delete($id);
        alert_success('Materia prima eliminada exitosamente');
        redirect(BASE_URL . '/raw-materials');
    }
}
