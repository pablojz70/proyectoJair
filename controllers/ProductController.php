<?php
class ProductController
{
    private $model;
    private $rawMaterialModel;

    public function __construct()
    {
        Session::requireLogin();
        $this->model = new Product();
        $this->rawMaterialModel = new RawMaterial();
    }

    public function index()
    {
        $pageTitle = 'Productos';
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $userId = Session::get('user_id');
        $products = $this->model->getAll($userId, $type, $search);

        ob_start();
        require __DIR__ . '/../views/products/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function create()
    {
        $pageTitle = 'Nuevo Producto';
        $type = $_GET['type'] ?? 'simple';
        $userId = Session::get('user_id');
        $materials = $this->rawMaterialModel->getAll($userId);

        ob_start();
        require __DIR__ . '/../views/products/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/products');
        }

        $type = $_POST['type'] ?? 'simple';
        $data = [
            'user_id' => Session::get('user_id'),
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'type' => $type,
            'sale_price_usd' => (float) ($_POST['sale_price_usd'] ?? 0),
            'production_cost_usd' => null,
        ];

        if ($type === 'simple') {
            $data['stock'] = (float) ($_POST['stock'] ?? 0);
        } else {
            $data['stock'] = null;
        }

        if (empty($data['name'])) {
            alert_error('El nombre es requerido');
            redirect(BASE_URL . '/products/create?type=' . $type);
        }

        if ($data['sale_price_usd'] <= 0) {
            alert_error('El precio de venta debe ser mayor a 0');
            redirect(BASE_URL . '/products/create?type=' . $type);
        }

        $productId = $this->model->create($data);

        if ($type === 'compuesto' && $productId) {
            $materials = $_POST['materials'] ?? [];
            $quantities = $_POST['quantities'] ?? [];

            if (is_array($materials)) {
                foreach ($materials as $i => $matId) {
                    if (!empty($matId) && isset($quantities[$i]) && $quantities[$i] > 0) {
                        $this->model->addRecipeItem($productId, $matId, (float) $quantities[$i]);
                    }
                }
                $this->model->updateProductionCost($productId);
                $data['production_cost_usd'] = $this->model->calculateProductionCost($productId);
                $this->model->update($productId, $data);
            }
        }

        alert_success('Producto creado exitosamente');
        redirect(BASE_URL . '/products');
    }

    public function edit()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/products');
        }

        $product = $this->model->findById($id);
        if (!$product) {
            alert_error('Producto no encontrado');
            redirect(BASE_URL . '/products');
        }

        $pageTitle = 'Editar Producto';
        $userId = Session::get('user_id');
        $materials = $this->rawMaterialModel->getAll($userId);
        $recipe = $this->model->getRecipe($id);

        ob_start();
        require __DIR__ . '/../views/products/form.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function update()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/products');
        }

        $product = $this->model->findById($id);
        if (!$product) {
            alert_error('Producto no encontrado');
            redirect(BASE_URL . '/products');
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'type' => $product['type'],
            'sale_price_usd' => (float) ($_POST['sale_price_usd'] ?? 0),
        ];

        if ($product['type'] === 'simple') {
            $data['stock'] = (float) ($_POST['stock'] ?? 0);
        }

        if ($product['type'] === 'compuesto') {
            $materials = $_POST['materials'] ?? [];
            $quantities = $_POST['quantities'] ?? [];

            $existingRecipe = $this->model->getRecipe($id);
            $existingIds = array_column($existingRecipe, 'raw_material_id');

            foreach ($existingRecipe as $item) {
                $this->model->removeRecipeItem($item['id']);
            }

            if (is_array($materials)) {
                foreach ($materials as $i => $matId) {
                    if (!empty($matId) && isset($quantities[$i]) && (float) $quantities[$i] > 0) {
                        $this->model->addRecipeItem($id, $matId, (float) $quantities[$i]);
                    }
                }
            }

            $data['production_cost_usd'] = $this->model->calculateProductionCost($id);
            $this->model->updateProductionCost($id);
        }

        $this->model->update($id, $data);
        alert_success('Producto actualizado exitosamente');
        redirect(BASE_URL . '/products');
    }

    public function delete()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/products');
        }

        $this->model->delete($id);
        alert_success('Producto eliminado exitosamente');
        redirect(BASE_URL . '/products');
    }

    public function recipe()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id) {
            redirect(BASE_URL . '/products');
        }

        $product = $this->model->findById($id);
        if (!$product || $product['type'] !== 'compuesto') {
            alert_error('Producto no encontrado o no es compuesto');
            redirect(BASE_URL . '/products');
        }

        $pageTitle = 'Receta: ' . $product['name'];
        $userId = Session::get('user_id');
        $materials = $this->rawMaterialModel->getAll($userId);
        $recipe = $this->model->getRecipe($id);
        $productionCost = $this->model->calculateProductionCost($id);

        ob_start();
        require __DIR__ . '/../views/products/recipe.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function addRecipeItem()
    {
        $id = $_GET['params'][0] ?? null;
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/products');
        }

        $materialId = (int) ($_POST['material_id'] ?? 0);
        $quantity = (float) ($_POST['quantity'] ?? 0);

        if ($materialId <= 0 || $quantity <= 0) {
            alert_error('Datos de receta invalidos');
            redirect(BASE_URL . "/products/recipe/{$id}");
        }

        $this->model->addRecipeItem($id, $materialId, $quantity);
        $this->model->updateProductionCost($id);
        alert_success('Ingrediente agregado a la receta');
        redirect(BASE_URL . "/products/recipe/{$id}");
    }

    public function removeRecipeItem()
    {
        $itemId = $_GET['params'][0] ?? null;
        $productId = $_GET['params'][1] ?? null;

        if (!$itemId || !$productId) {
            redirect(BASE_URL . '/products');
        }

        $this->model->removeRecipeItem($itemId);
        $this->model->updateProductionCost($productId);
        alert_success('Ingrediente eliminado de la receta');
        redirect(BASE_URL . "/products/recipe/{$productId}");
    }
}
