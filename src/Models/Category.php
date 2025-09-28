<?php

declare(strict_types=1);

namespace Swifty\Models;

use Swifty\Exceptions\DatabaseException;
use Swifty\Exceptions\ValidationException;

/**
 * Category model for managing post categories
 */
class Category extends BaseModel
{
    protected function getTable(): string
    {
        return 'categories';
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => [
                'required' => true,
                'type' => 'string',
                'max_length' => 100,
                'min_length' => 2
            ]
        ];
    }

    protected function getFillableFields(): array
    {
        return ['name'];
    }

    /**
     * Create a new category
     * 
     * @throws ValidationException|DatabaseException
     */
    public function create(array $data): int
    {
        $this->validate($data);
        $sanitizedData = $this->sanitize($data);

        // Check for duplicate name
        if ($this->nameExists($sanitizedData['name'])) {
            throw new ValidationException(['name' => 'Category name already exists']);
        }

        $query = "INSERT INTO {$this->getTable()} SET name = :name";
        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt, [':name' => $sanitizedData['name']]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Update an existing category
     * 
     * @throws ValidationException|DatabaseException
     */
    public function update(int $id, array $data): bool
    {
        $this->validate($data);
        $sanitizedData = $this->sanitize($data);

        // Check if category exists
        if (!$this->findById($id)) {
            throw new ValidationException(['id' => 'Category not found']);
        }

        // Check for duplicate name (excluding current record)
        if ($this->nameExists($sanitizedData['name'], $id)) {
            throw new ValidationException(['name' => 'Category name already exists']);
        }

        $query = "UPDATE {$this->getTable()} SET name = :name WHERE id = :id";
        $stmt = $this->connection->prepare($query);
        
        return $this->executeStatement($stmt, [
            ':id' => $id,
            ':name' => $sanitizedData['name']
        ]);
    }

    /**
     * Delete a category (only if no posts are associated)
     * 
     * @throws ValidationException|DatabaseException
     */
    public function deleteById(int $id): bool
    {
        // Check if category exists
        if (!$this->findById($id)) {
            throw new ValidationException(['id' => 'Category not found']);
        }

        // Check if category has associated posts
        if ($this->hasAssociatedPosts($id)) {
            throw new ValidationException(['id' => 'Cannot delete category with associated posts']);
        }

        return parent::deleteById($id);
    }

    /**
     * Check if category name already exists
     */
    private function nameExists(string $name, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) FROM {$this->getTable()} WHERE name = ?";
        $params = [$name];

        if ($excludeId !== null) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt, $params);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if category has associated posts
     */
    private function hasAssociatedPosts(int $categoryId): bool
    {
        $query = "SELECT COUNT(*) FROM posts WHERE category_id = ?";
        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt, [$categoryId]);
        
        return $stmt->fetchColumn() > 0;
    }
}