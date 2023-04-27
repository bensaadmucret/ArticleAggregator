<?php

declare(strict_types=1);

namespace Alltricks;

use PDO;
use Exception;
use PDOException;
use Dotenv\Dotenv;



class dbConnexion
{
    private PDO $db;
 


    public function __construct(string $dbHost = 'localhost', ?string $dbName = null, ?string $dbUser = null, ?string $dbPassword = null)
    {
        $envFile = dirname(__DIR__) . '/.env';
    
        if (file_exists($envFile)) {
            $envVars = parse_ini_file($envFile);
            
            /**
             * @var array<string, string> $envVars
             */
            foreach ($envVars as $key => $value) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        } else {
            throw new Exception('Le fichier .env est manquant.');
        }
        $dbName = $dbName ?? $_ENV['DB_NAME'] ?? '';
        $dbUser = $dbUser ?? $_ENV['DB_USER'] ?? '';
        $dbPassword = $dbPassword ?? $_ENV['DB_PASSWORD'] ?? '';
        try {

            $this->db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
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
