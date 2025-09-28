<?php

declare(strict_types=1);

namespace Swifty\Config;

use PDO;
use PDOException;
use Swifty\Exceptions\DatabaseException;

/**
 * Database connection handler with improved error handling and multi-driver support
 * Supports MySQL, PostgreSQL, SQLite, and other PDO-compatible databases
 */
class Database
{
    private static ?PDO $connection = null;
    
    private string $driver;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $dbname;
    private string $charset;

    public function __construct(
        string $driver = 'mysql',
        string $host = 'localhost',
        int $port = 3306,
        string $username = 'root',
        string $password = '',
        string $dbname = 'swifty',
        string $charset = 'utf8mb4'
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
            $dsn = $this->buildDsn();
            $options = $this->getConnectionOptions();
            
            self::$connection = new PDO($dsn, $this->username, $this->password, $options);

            return self::$connection;
        } catch (PDOException $e) {
            throw new DatabaseException("Connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
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
            
            case 'oci':
            case 'oracle':
                return "oci:dbname=//{$this->host}:{$this->port}/{$this->dbname};charset={$this->charset}";
            
            default:
                throw new DatabaseException("Unsupported database driver: {$this->driver}");
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
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        switch ($this->driver) {
            case 'mysql':
                $baseOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$this->charset}";
                break;
            
            case 'pgsql':
            case 'postgresql':
                // PostgreSQL-specific options can be added here
                break;
            
            case 'sqlite':
                // SQLite-specific options can be added here
                break;
        }

        return $baseOptions;
    }

    /**
     * Create database instance from environment variables (Laravel-style)
     */
    public static function fromEnv(): self
    {
        $connection = $_ENV['DB_CONNECTION'] ?? $_ENV['DATABASE_CONNECTION'] ?? $_ENV['DB_DRIVER'] ?? 'mysql';
        
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
        $dbPath = $_ENV['DB_DATABASE'] ?? 'db.sqlite';
        
        // If it's just a filename, assume it's in the project root
        if (!str_contains($dbPath, '/') && !str_contains($dbPath, '\\')) {
            $dbPath = __DIR__ . '/../../' . $dbPath;
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
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            (int) ($_ENV['DB_PORT'] ?? 3306),
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_DATABASE'] ?? 'swifty',
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
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            (int) ($_ENV['DB_PORT'] ?? 5432),
            $_ENV['DB_USERNAME'] ?? 'postgres',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_DATABASE'] ?? 'swifty',
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
            $_ENV['DB_HOST'] ?? '127.0.0.1',
            (int) ($_ENV['DB_PORT'] ?? 3306),
            $_ENV['DB_USERNAME'] ?? 'root',
            $_ENV['DB_PASSWORD'] ?? '',
            $_ENV['DB_DATABASE'] ?? 'swifty',
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

    /**
     * Check if the current driver is SQLite
     */
    public function isSqlite(): bool
    {
        return $this->driver === 'sqlite';
    }

    /**
     * Check if the current driver is MySQL
     */
    public function isMysql(): bool
    {
        return $this->driver === 'mysql';
    }

    /**
     * Check if the current driver is PostgreSQL
     */
    public function isPostgresql(): bool
    {
        return in_array($this->driver, ['pgsql', 'postgresql']);
    }

    /**
     * Get database-specific SQL syntax for LIMIT with offset
     */
    public function getLimitSyntax(int $limit, int $offset = 0): string
    {
        switch ($this->driver) {
            case 'mysql':
            case 'sqlite':
                return $offset > 0 ? "LIMIT {$offset}, {$limit}" : "LIMIT {$limit}";
            
            case 'pgsql':
            case 'postgresql':
                return $offset > 0 ? "LIMIT {$limit} OFFSET {$offset}" : "LIMIT {$limit}";
            
            default:
                return "LIMIT {$limit}";
        }
    }

    /**
     * Get the last inserted ID, compatible with different drivers
     */
    public function getLastInsertId(?string $sequence = null): string
    {
        if ($this->isPostgresql() && $sequence) {
            return self::$connection->lastInsertId($sequence);
        }
        
        return self::$connection->lastInsertId();
    }

    /**
     * Close the database connection
     */
    public static function close(): void
    {
        self::$connection = null;
    }
}