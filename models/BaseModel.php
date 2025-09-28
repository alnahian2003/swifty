<?php

declare(strict_types=1);

/**
 * Base model class for legacy models providing common database operations
 * This follows DRY principles by centralizing common functionality
 * Supports flexible primary key configuration like Laravel
 * 
 * Usage Examples:
 * 
 * Default usage (id as primary key):
 * class Post extends BaseModel { ... }
 * 
 * Custom primary key:
 * class Product extends BaseModel {
 *     protected string $primaryKey = 'slug'; // Use slug as primary key
 *     public string $slug;
 *     
 *     protected function getTableName(): string {
 *         return 'products';
 *     }
 * }
 * 
 * class User extends BaseModel {
 *     protected string $primaryKey = 'email'; // Use email as primary key
 *     public string $email;
 *     
 *     protected function getTableName(): string {
 *         return 'users';
 *     }
 * }
 */
abstract class BaseModel
{
    protected PDO $conn;
    protected string $table;
    protected string $primaryKey = 'id'; // Default primary key

    public function __construct(PDO $db)
    {
        $this->conn = $db;
        $this->table = $this->getTableName();
    }

    /**
     * Get the table name for this model
     */
    abstract protected function getTableName(): string;

    /**
     * Get the primary key column name
     */
    protected function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Set the primary key column name
     */
    protected function setPrimaryKey(string $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * Get all records from the table
     */
    public function read(): PDOStatement
    {
        $primaryKey = $this->getPrimaryKey();
        $query = "SELECT * FROM {$this->table} ORDER BY {$primaryKey} DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get a single record by primary key value
     */
    abstract public function get($keyValue): bool;

    /**
     * Create a new record
     */
    abstract public function create(): bool;

    /**
     * Update an existing record
     */
    abstract public function update(): bool;

    /**
     * Delete a record by primary key value
     */
    public function delete(): bool
    {
        $primaryKey = $this->getPrimaryKey();
        $query = "DELETE FROM {$this->table} WHERE {$primaryKey} = :key_value";
        $stmt = $this->conn->prepare($query);
        
        // Get the primary key value from the object property
        $keyValue = $this->getPrimaryKeyValue();
        $keyValue = $this->sanitizeValue($keyValue);
        
        $stmt->bindParam(":key_value", $keyValue);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the primary key value from the object
     */
    protected function getPrimaryKeyValue()
    {
        $primaryKey = $this->getPrimaryKey();
        return $this->$primaryKey ?? null;
    }

    /**
     * Set the primary key value on the object
     */
    protected function setPrimaryKeyValue($value): void
    {
        $primaryKey = $this->getPrimaryKey();
        $this->$primaryKey = $value;
    }

    /**
     * Sanitize value based on type
     */
    protected function sanitizeValue($value)
    {
        if (is_string($value)) {
            return $this->sanitizeString($value);
        } elseif (is_numeric($value)) {
            return $this->sanitizeInt($value);
        }
        return $value;
    }

    /**
     * Sanitize string input
     */
    protected function sanitizeString(?string $input): string
    {
        if ($input === null) {
            return '';
        }
        
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize integer input
     */
    protected function sanitizeInt($input): int
    {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Execute a prepared statement with error handling
     */
    protected function executeStatement(PDOStatement $stmt): bool
    {
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}