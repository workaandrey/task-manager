<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database connection manager
 */
final class Database 
{
    /**
     * @var PDO|null Database connection instance
     */
    private ?PDO $conn = null;
    
    /**
     * Get a database connection
     * 
     * @return PDO The database connection
     * @throws PDOException If connection fails
     */
    public function getConnection(): PDO
    {
        if ($this->conn === null) {
            $this->connect();
        }
        
        return $this->conn;
    }
    
    /**
     * Create a new database connection
     * 
     * @return void
     * @throws PDOException If connection fails
     */
    private function connect(): void
    {
        if (!Config::has('DB_HOST')) {
            Config::load();
        }
        
        $host = Config::get('DB_HOST');
        $dbName = Config::get('DB_NAME');
        $user = Config::get('DB_USER');
        $pass = Config::get('DB_PASS');
        $charset = Config::get('DB_CHARSET', 'utf8mb4');
        
        $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException(
                "Database connection failed: " . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }
}