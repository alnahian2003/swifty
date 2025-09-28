<?php

declare(strict_types=1);

namespace Swifty\Models;

use PDO;
use Swifty\Exceptions\DatabaseException;
use Swifty\Exceptions\ValidationException;

/**
 * Post model for managing blog posts
 */
class Post extends BaseModel
{
    protected function getTable(): string
    {
        return 'posts';
    }

    protected function getValidationRules(): array
    {
        return [
            'title' => [
                'required' => true,
                'type' => 'string',
                'max_length' => 255,
                'min_length' => 3
            ],
            'body' => [
                'required' => true,
                'type' => 'string',
                'min_length' => 10
            ],
            'author' => [
                'required' => true,
                'type' => 'string',
                'max_length' => 100,
                'min_length' => 2
            ],
            'category_id' => [
                'required' => true,
                'type' => 'int'
            ]
        ];
    }

    protected function getFillableFields(): array
    {
        return ['title', 'body', 'author', 'category_id'];
    }

    /**
     * Get all posts with category information
     */
    public function getAllWithCategory(): array
    {
        $query = "
            SELECT 
                c.name as category_name,
                p.id,
                p.category_id,
                p.title,
                p.body,
                p.author,
                p.created_at
            FROM {$this->getTable()} p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single post with category information
     */
    public function getByIdWithCategory(int $id): ?array
    {
        $query = "
            SELECT 
                c.name as category_name,
                p.id,
                p.category_id,
                p.title,
                p.body,
                p.author,
                p.created_at
            FROM {$this->getTable()} p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?
            LIMIT 1
        ";

        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt, [$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Create a new post
     * 
     * @throws ValidationException|DatabaseException
     */
    public function create(array $data): int
    {
        $this->validate($data);
        $sanitizedData = $this->sanitize($data);

        // Check if category exists
        if (!$this->categoryExists($sanitizedData['category_id'])) {
            throw new ValidationException(['category_id' => 'Category does not exist']);
        }

        $query = "
            INSERT INTO {$this->getTable()} 
            SET title = :title, category_id = :category_id, body = :body, author = :author
        ";

        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt, [
            ':title' => $sanitizedData['title'],
            ':category_id' => $sanitizedData['category_id'],
            ':body' => $sanitizedData['body'],
            ':author' => $sanitizedData['author']
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Update an existing post
     * 
     * @throws ValidationException|DatabaseException
     */
    public function update(int $id, array $data): bool
    {
        $this->validate($data);
        $sanitizedData = $this->sanitize($data);

        // Check if post exists
        if (!$this->findById($id)) {
            throw new ValidationException(['id' => 'Post not found']);
        }

        // Check if category exists
        if (isset($sanitizedData['category_id']) && !$this->categoryExists($sanitizedData['category_id'])) {
            throw new ValidationException(['category_id' => 'Category does not exist']);
        }

        $query = "
            UPDATE {$this->getTable()} 
            SET title = :title, category_id = :category_id, body = :body, author = :author 
            WHERE id = :id
        ";

        $stmt = $this->connection->prepare($query);
        return $this->executeStatement($stmt, [
            ':id' => $id,
            ':title' => $sanitizedData['title'],
            ':category_id' => $sanitizedData['category_id'],
            ':body' => $sanitizedData['body'],
            ':author' => $sanitizedData['author']
        ]);
    }

    /**
     * Check if a category exists
     */
    private function categoryExists(int $categoryId): bool
    {
        $query = "SELECT COUNT(*) FROM categories WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $this->executeStatement($stmt, [$categoryId]);
        
        return $stmt->fetchColumn() > 0;
    }
}