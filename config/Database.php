<?php

declare(strict_types=1);

/**
 * Legacy Database class - maintained for backward compatibility
 * For new code, use Swifty\Config\Database instead
 * 
 * @deprecated Use Swifty\Config\Database instead
 */
class Database
{
    private string $host;
    private string $username;
    private string $password;
    private string $dbname;
    private ?PDO $conn = null;

    public function __construct(
        string $host = "localhost",
        string $username = "root", 
        string $password = "",
        string $dbname = "swifty"
    ) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }

    /**
     * Connect to the Database with improved error handling
     */
    public function connect(): ?PDO
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);

            return $this->conn;
        } catch (PDOException $e) {
            // Log error instead of echoing for production
            error_log("Database connection error: " . $e->getMessage());
            
            // For backward compatibility, still echo the error
            echo "Connection Error: " . $e->getMessage();
            return null;
        }
    }
}
