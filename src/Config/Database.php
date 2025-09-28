<?php

declare(strict_types=1);

namespace Swifty\Config;

use PDO;
use PDOException;
use Swifty\Exceptions\DatabaseException;

/**
 * Database connection handler with improved error handling and configuration
 */
class Database
{
    private static ?PDO $connection = null;
    
    private string $host;
    private string $username;
    private string $password;
    private string $dbname;
    private string $charset;

    public function __construct(
        string $host = 'localhost',
        string $username = 'root',
        string $password = '',
        string $dbname = 'swifty',
        string $charset = 'utf8mb4'
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->charset = $charset;
    }

    /**
     * Create database connection with proper error handling
     * 
     * @throws DatabaseException
     */
    public function connect(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            
            self::$connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ]);

            return self::$connection;
        } catch (PDOException $e) {
            throw new DatabaseException("Connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Create database instance from environment variables
     */
    public static function fromEnv(): self
    {
        return new self(
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_NAME'] ?? 'swifty',
            $_ENV['DB_CHARSET'] ?? 'utf8mb4'
        );
    }

    /**
     * Close the database connection
     */
    public static function close(): void
    {
        self::$connection = null;
    }
}