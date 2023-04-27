<?php


declare (strict_types=1);

namespace Alltricks;

use PDO;
use PDOException;

final class dBConnexion
{
    private PDO $db;
    private string $dbHost;
    private string $dbName;
    private string $dbUser;
    private string $dbPassword;

    public function __construct()
    {
        $this->dbHost = $_ENV['DB_HOST'] ?? 'localhost';
        $this->dbName = $_ENV['DB_NAME'] ?? '';
        $this->dbUser = $_ENV['DB_USER'] ?? '';
        $this->dbPassword = $_ENV['DB_PASSWORD'] ?? '';
        try {
            $this->db = new PDO("mysql:host=$this->dbHost;dbname=$this->dbName", $this->dbUser, $this->dbPassword);
        } catch (PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
            exit;
        }
    }

    public function getPdo(): PDO
    {
        return $this->db;
    }
}
