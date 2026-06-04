<?php
class BackupController
{
    public function index()
    {
        Session::requireAdmin();
        $pageTitle = 'Respaldo de Base de Datos';
        $db = Database::getInstance();

        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $backupDir = __DIR__ . '/../backups';
        if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
        $backups = glob($backupDir . '/*.sql');
        rsort($backups);

        ob_start();
        require __DIR__ . '/../views/backup/index.php';
        $content = ob_get_clean();
        require __DIR__ . '/../views/layouts/header.php';
        echo $content;
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function download()
    {
        Session::requireAdmin();
        $params = $_GET['params'] ?? [];

        $backupDir = __DIR__ . '/../backups';

        // If a filename is given, download existing backup
        if (!empty($params[0])) {
            $file = $backupDir . '/' . basename($params[0]);
            if (!file_exists($file)) {
                alert_error('Archivo de respaldo no encontrado');
                redirect(BASE_URL . '/backup');
            }
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }

        // Generate new backup
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $sql = "-- Respaldo del Sistema de Ventas\n";
        $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
            $sql .= "\n-- Estructura de tabla: `$table`\n";
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $create['Create Table'] . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) continue;

            $sql .= "-- Datos de tabla: `$table`\n";
            foreach ($rows as $row) {
                $cols = array_map(function($c) { return "`$c`"; }, array_keys($row));
                $vals = array_map(function($v) use ($pdo) {
                    if ($v === null) return 'NULL';
                    return $pdo->quote($v);
                }, array_values($row));
                $sql .= "INSERT INTO `$table` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        $filename = 'respaldo_' . date('Y-m-d_H-i-s') . '.sql';

        if (!is_dir($backupDir)) mkdir($backupDir, 0777, true);
        file_put_contents($backupDir . '/' . $filename, $sql);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sql));
        echo $sql;
        exit;
    }

    public function restore()
    {
        Session::requireAdmin();
        $backupDir = __DIR__ . '/../backups';
        $params = $_GET['params'] ?? [];

        // Determine source: uploaded file or existing backup
        if (!empty($_FILES['backup_file']['tmp_name'])) {
            $sql = file_get_contents($_FILES['backup_file']['tmp_name']);
        } elseif (!empty($params[0])) {
            $file = $backupDir . '/' . basename($params[0]);
            if (!file_exists($file)) {
                alert_error('Archivo de respaldo no encontrado');
                redirect(BASE_URL . '/backup');
            }
            $sql = file_get_contents($file);
        } else {
            alert_error('No se selecciono ningun archivo');
            redirect(BASE_URL . '/backup');
        }

        if (empty(trim($sql))) {
            alert_error('El archivo esta vacio');
            redirect(BASE_URL . '/backup');
        }

        $db = Database::getInstance();
        $pdo = $db->getConnection();

        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            $statements = explode(";\n", $sql);
            $count = 0;
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt) || strpos($stmt, '--') === 0) continue;
                try {
                    $pdo->exec($stmt);
                    $count++;
                } catch (PDOException $e) {
                    // Skip duplicate key errors and already-exists errors
                    if (strpos($e->getMessage(), 'Duplicate') === false && strpos($e->getMessage(), 'already exists') === false) {
                        throw $e;
                    }
                }
            }

            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            alert_success("Restauracion completada. {$count} sentencias ejecutadas.");
        } catch (Exception $e) {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            alert_error('Error durante la restauracion: ' . $e->getMessage());
        }

        redirect(BASE_URL . '/backup');
    }
}
