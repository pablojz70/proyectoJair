<?php
class PaymentController
{
    private $paymentModel;
    private $clientModel;
    private $saleModel;

    public function __construct()
    {
        Session::requireLogin();
        $this->paymentModel = new Payment();
        $this->clientModel = new Client();
        $this->saleModel = new Sale();
    }

    public function index()
    {
        $pageTitle = 'Pagos y Deudas';
        $userId = Session::get('user_id');
        $search = $_GET['search'] ?? '';

        $debts = $this->paymentModel->getDebts($userId);

        if (!empty($search)) {
            $debts = array_filter($debts, function ($d) use ($search) {
                return stripos($d['client_name'], $search) !== false;
            });
        }

        $overdueClients = $this->paymentModel->getOverdueClients($userId);

        ob_start();
        require __DIR__ . '/../views/payments/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function client()
    {
        $clientId = $_GET['params'][0] ?? null;
        if (!$clientId) {
            redirect(BASE_URL . '/payments');
        }

        $userId = Session::get('user_id');
        $client = $this->clientModel->findById($clientId, $userId);

        if (!$client) {
            alert_error('Cliente no encontrado');
            redirect(BASE_URL . '/payments');
        }

        $debts = $this->paymentModel->getDebtsByClient($clientId, $userId);
        $payments = $this->paymentModel->getByClient($clientId, $userId);
        $exchangeRate = ExchangeRate::getRate();

        $totalDebt = 0;
        foreach ($debts as $d) {
            $totalDebt += $d['total_usd'] - $d['paid'];
        }

        $pageTitle = 'Deudas de ' . $client['full_name'];

        ob_start();
        require __DIR__ . '/../views/payments/client.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function pay()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/payments');
        }

        $clientId = (int) ($_POST['client_id'] ?? 0);
        $saleIds = $_POST['sale_ids'] ?? [];
        $amountUsd = (float) ($_POST['amount_usd'] ?? 0);
        $amountBs = (float) ($_POST['amount_bs'] ?? 0);
        $exchangeRate = (float) ($_POST['exchange_rate'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        if (empty($saleIds) || !is_array($saleIds)) {
            alert_error('Debe seleccionar al menos una venta');
            redirect(BASE_URL . "/payments/client/{$clientId}");
        }

        if ($exchangeRate <= 0) {
            $exchangeRate = ExchangeRate::getRate();
        }

        if ($amountUsd <= 0 && $amountBs > 0) {
            $amountUsd = $amountBs / $exchangeRate;
        } elseif ($amountUsd > 0 && $amountBs <= 0) {
            $amountBs = $amountUsd * $exchangeRate;
        }

        if ($amountUsd <= 0) {
            alert_error('El monto debe ser mayor a 0');
            redirect(BASE_URL . "/payments/client/{$clientId}");
        }

        $remaining = $amountUsd;

        foreach ($saleIds as $saleId) {
            if ($remaining <= 0) break;

            $sale = $this->saleModel->findById($saleId);
            if (!$sale) continue;

            $totalPaid = $this->paymentModel->getTotalPaid($saleId);
            $pending = $sale['total_usd'] - $totalPaid;

            if ($pending <= 0) continue;

            $payAmount = min($remaining, $pending);
            $payBs = $payAmount * $exchangeRate;

            try {
                $this->paymentModel->create([
                    'sale_id' => $saleId,
                    'amount_usd' => $payAmount,
                    'amount_bs' => $payBs,
                    'exchange_rate' => $exchangeRate,
                    'notes' => $notes,
                ]);
            } catch (Exception $e) {
                alert_error('Error al registrar pago: ' . $e->getMessage());
                redirect(BASE_URL . "/payments/client/{$clientId}");
            }

            $remaining -= $payAmount;
        }

        alert_success('Pago registrado exitosamente');
        redirect(BASE_URL . "/payments/client/{$clientId}");
    }
}
