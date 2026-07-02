<?php
declare(strict_types=1);

namespace OnlineBooking\src\Connection;

use PDO;
use PDOException;
use RuntimeException;

final class DBConnection
{
    private static ?PDO $instance = null;

    private function __construct(){}

    public static function getInstance() : PDO
    {
        if (self::$instance === null) {
            try {
                $host = $_ENV['DB_HOST'];
                $port = $_ENV['DB_PORT'];
                $dbname = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $password = $_ENV['DB_PASSWORD'];
                $charset = $_ENV['DB_CHARSET'];

                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

                self::$instance = new PDO($dsn, $user, $password);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new RuntimeException("An error occurred while connecting to the database.",
                    code: 500,
                    previous: $e);
            }
        }

        return self::$instance;
    }

    private function __clone(){}
    public function __wakeup(): void
    {
        throw new RuntimeException("Cannot unserialize a singleton.");
    }
}