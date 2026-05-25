<?php
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $driver = getenv('DB_DRIVER') ?: 'mysql';
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: ($driver === 'pgsql' ? '5432' : '3306');
        $dbname = getenv('DB_NAME') ?: 'ventas_recetas';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASS') ?: '';
        $sslMode = getenv('DB_SSLMODE') ?: 'prefer';

        try {
            if ($driver === 'pgsql') {
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslMode}";
            } else {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            }

            $opts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->pdo = new PDO($dsn, $username, $password, $opts);
        } catch (PDOException $e) {
            die("Error de conexion: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    public function query($sql)
    {
        return $this->pdo->query($sql);
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public static function driver()
    {
        return getenv('DB_DRIVER') ?: 'mysql';
    }
}
