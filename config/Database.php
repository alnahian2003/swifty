<?php

declare(strict_types=1);

/**
 * Legacy Database class - maintained for backward compatibility
 * For new code, use Swifty\Config\Database instead
 * Now supports multiple database drivers (MySQL, PostgreSQL, SQLite)
 * 
 * @deprecated Use Swifty\Config\Database instead
 */
class Database
{
    private string $driver;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $dbname;
    private string $charset;
    private ?PDO $conn = null;

    public function __construct(
        string $driver = "mysql",
        string $host = "localhost",
        int $port = 3306,
        string $username = "root", 
        string $password = "",
        string $dbname = "swifty",
        string $charset = "utf8mb4"
    ) {
        $this->driver = strtolower($driver);
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->charset = $charset;
    }

    /**
     * Connect to the Database with improved error handling and multi-driver support
     */
    public function connect(): ?PDO
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $dsn = $this->buildDsn();
            $options = $this->getConnectionOptions();
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

            return $this->conn;
        } catch (PDOException $e) {
            // Log error instead of echoing for production
            error_log("Database connection error: " . $e->getMessage());
            
            // For backward compatibility, still echo the error
            echo "Connection Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Build DSN string based on the database driver
     */
    private function buildDsn(): string
    {
        switch ($this->driver) {
            case 'mysql':
                return "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            
            case 'pgsql':
            case 'postgresql':
                return "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
            
            case 'sqlite':
                // For SQLite, dbname is the file path
                return "sqlite:{$this->dbname}";
            
            case 'sqlsrv':
            case 'mssql':
                return "sqlsrv:Server={$this->host},{$this->port};Database={$this->dbname}";
            
            default:
                throw new Exception("Unsupported database driver: {$this->driver}");
        }
    }

    /**
     * Get connection options based on the database driver
     */
    private function getConnectionOptions(): array
    {
        $baseOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        switch ($this->driver) {
            case 'mysql':
                $baseOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$this->charset}";
                break;
        }

        return $baseOptions;
    }

    /**
     * Create database instance from environment variables (Laravel-style)
     */
    public static function fromEnv(): self
    {
        $connection = $_ENV['DATABASE_CONNECTION'] ?? $_ENV['DB_DRIVER'] ?? 'mysql';
        
        // Laravel-style convention over configuration
        switch (strtolower($connection)) {
            case 'sqlite':
                return self::createSqliteConnection();
            
            case 'mysql':
                return self::createMysqlConnection();
            
            case 'pgsql':
            case 'postgresql':
                return self::createPostgresqlConnection();
            
            default:
                return self::createCustomConnection($connection);
        }
    }

    /**
     * Create SQLite connection with Laravel conventions
     */
    private static function createSqliteConnection(): self
    {
        // Laravel convention: use database/database.sqlite or db.sqlite in root
        $dbPath = $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? 'db.sqlite';
        
        // If it's just a filename, assume it's in the project root
        if (!str_contains($dbPath, '/') && !str_contains($dbPath, '\\')) {
            $dbPath = __DIR__ . '/../' . $dbPath;
        }
        
        return new self(
            'sqlite',
            '', // Not used for SQLite
            0,  // Not used for SQLite
            '', // Not used for SQLite
            '', // Not used for SQLite
            $dbPath,
            'utf8'
        );
    }

    /**
     * Create MySQL connection with environment configuration
     */
    private static function createMysqlConnection(): self
    {
        return new self(
            'mysql',
            $_ENV['DB_HOST'] ?? 'localhost',
            (int) ($_ENV['DB_PORT'] ?? 3306),
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? 'swifty',
            $_ENV['DB_CHARSET'] ?? 'utf8mb4'
        );
    }

    /**
     * Create PostgreSQL connection with environment configuration
     */
    private static function createPostgresqlConnection(): self
    {
        return new self(
            'pgsql',
            $_ENV['DB_HOST'] ?? 'localhost',
            (int) ($_ENV['DB_PORT'] ?? 5432),
            $_ENV['DB_USERNAME'] ?? 'postgres',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? 'swifty',
            $_ENV['DB_CHARSET'] ?? 'utf8'
        );
    }

    /**
     * Create custom connection for other database types
     */
    private static function createCustomConnection(string $driver): self
    {
        return new self(
            $driver,
            $_ENV['DB_HOST'] ?? 'localhost',
            (int) ($_ENV['DB_PORT'] ?? 3306),
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_DATABASE'] ?? $_ENV['DB_NAME'] ?? 'swifty',
            $_ENV['DB_CHARSET'] ?? 'utf8mb4'
        );
    }

    /**
     * Get the current database driver
     */
    public function getDriver(): string
    {
        return $this->driver;
    }
}
