<?php

declare(strict_types=1);

namespace Swifty\Models;

use PDO;
use PDOStatement;
use Swifty\Exceptions\DatabaseException;
use Swifty\Exceptions\ValidationException;

/**
 * Base model class providing common database operations
 */
abstract class BaseModel
{
    protected PDO $connection;
    protected string $table;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the table name for this model
     */
    abstract protected function getTable(): string;

    /**
     * Get validation rules for the model
     */
    abstract protected function getValidationRules(): array;

    /**
     * Get fillable fields for the model
     */
    abstract protected function getFillableFields(): array;

    /**
     * Validate data against model rules
     * 
     * @throws ValidationException
     */
    protected function validate(array $data): void
    {
        $rules = $this->getValidationRules();
        $errors = [];

        foreach ($rules as $field => $rule) {
            if (isset($rule['required']) && $rule['required'] && (!isset($data[$field]) || empty($data[$field]))) {
                $errors[$field] = "{$field} is required";
                continue;
            }

            if (!isset($data[$field])) {
                continue;
            }

            $value = $data[$field];

            // Type validation
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'int':
                        if (!is_numeric($value)) {
                            $errors[$field] = "{$field} must be a number";
                        }
                        break;
                    case 'string':
                        if (!is_string($value)) {
                            $errors[$field] = "{$field} must be a string";
                        }
                        break;
                }
            }

            // Length validation
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = "{$field} must not exceed {$rule['max_length']} characters";
            }

            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = "{$field} must be at least {$rule['min_length']} characters";
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Sanitize input data
     */
    protected function sanitize(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Execute a prepared statement with error handling
     * 
     * @throws DatabaseException
     */
    protected function executeStatement(PDOStatement $stmt, array $params = []): bool
    {
        try {
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            throw new DatabaseException("Query execution failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Find all records
     */
    public function findAll(string $orderBy = 'id DESC'): array
    {
        $query = "SELECT * FROM {$this->getTable()} ORDER BY {$orderBy}";
        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a single record by ID
     */
    public function findById(int $id): ?array
    {
        $query = "SELECT * FROM {$this->getTable()} WHERE id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt, [$id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Delete a record by ID
     * 
     * @throws DatabaseException
     */
    public function deleteById(int $id): bool
    {
        $query = "DELETE FROM {$this->getTable()} WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        
        return $this->executeStatement($stmt, [$id]);
    }
}