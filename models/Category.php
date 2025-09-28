<?php

declare(strict_types=1);

include_once __DIR__ . '/BaseModel.php';

/**
 * Legacy Category model - maintained for backward compatibility
 * For new code, use Swifty\Models\Category instead
 * 
 * @deprecated Use Swifty\Models\Category instead
 */
class Category extends BaseModel
{
    // Category Properties
    public ?int $id = null;
    public ?string $name = null;
    public ?string $title = null; // For backward compatibility

    protected function getTableName(): string
    {
        return "categories";
    }

    // Get a Single Category
    public function get($keyValue): bool
    {
        $primaryKey = $this->getPrimaryKey();
        $query = "
            SELECT * 
            FROM {$this->table} 
            WHERE {$primaryKey} = ?
            LIMIT 1
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);
        $sanitizedKey = $this->sanitizeValue($keyValue);
        $stmt->bindParam(1, $sanitizedKey);

        if ($stmt->execute()) {
            // Get the category
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($category) {
                $this->id = $category["id"] ?? null;
                $this->name = $category["name"] ?? null;
                $this->title = $category["name"] ?? null; // For backward compatibility
                
                // Set the primary key value
                $this->setPrimaryKeyValue($category[$primaryKey]);
                
                return true;
            }
        }

        return false;
    }

    // Create a Category
    public function create(): bool
    {
        $query = "
            INSERT INTO {$this->table} 
            SET name = :name
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = $this->sanitizeString($this->name);

        // Bind Data
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);

        return $this->executeStatement($stmt);
    }

    // Update a Category
    public function update(): bool
    {
        $query = "
            UPDATE {$this->table} 
            SET name = :name 
            WHERE id = :id
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = $this->sanitizeInt($this->id);
        $this->name = $this->sanitizeString($this->name);

        // Bind Data
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);

        return $this->executeStatement($stmt);
    }
}
