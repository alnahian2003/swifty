<?php

declare(strict_types=1);

/**
 * Base model class for legacy models providing common database operations
 * This follows DRY principles by centralizing common functionality
 */
abstract class BaseModel
{
    protected PDO $conn;
    protected string $table;

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
     * Get all records from the table
     */
    public function read(): PDOStatement
    {
        $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get a single record by ID
     */
    abstract public function get(int $id): bool;

    /**
     * Create a new record
     */
    abstract public function create(): bool;

    /**
     * Update an existing record
     */
    abstract public function update(): bool;

    /**
     * Delete a record by ID
     */
    public function delete(): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = $this->sanitizeInt($this->id);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
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