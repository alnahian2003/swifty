<?php

declare(strict_types=1);

include_once __DIR__ . '/BaseModel.php';

/**
 * Example Product model demonstrating custom primary key usage
 * This model uses 'slug' as the primary key instead of 'id'
 * 
 * Usage:
 * $product = new Product($db);
 * $product->get('my-product-slug'); // Retrieves by slug instead of id
 * $product->slug = 'new-product-slug';
 * $product->delete(); // Deletes by slug
 */
class Product extends BaseModel
{
    // Set custom primary key
    protected string $primaryKey = 'slug';

    // Product Properties
    public ?int $id = null;
    public ?string $slug = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?float $price = null;
    public ?string $created_at = null;

    protected function getTableName(): string
    {
        return "products";
    }

    // Get a Single Product by slug
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
            // Get the product
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $this->id = $product["id"] ?? null;
                $this->slug = $product["slug"] ?? null;
                $this->name = $product["name"] ?? null;
                $this->description = $product["description"] ?? null;
                $this->price = $product["price"] ?? null;
                $this->created_at = $product["created_at"] ?? null;
                
                // Set the primary key value
                $this->setPrimaryKeyValue($product[$primaryKey]);
                
                return true;
            }
        }

        return false;
    }

    // Create a Product
    public function create(): bool
    {
        $query = "
            INSERT INTO {$this->table} 
            SET slug = :slug, name = :name, description = :description, price = :price
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->slug = $this->sanitizeString($this->slug);
        $this->name = $this->sanitizeString($this->name);
        $this->description = $this->sanitizeString($this->description);
        $this->price = (float) $this->price;

        // Bind Data
        $stmt->bindParam(":slug", $this->slug, PDO::PARAM_STR);
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":description", $this->description, PDO::PARAM_STR);
        $stmt->bindParam(":price", $this->price);

        return $this->executeStatement($stmt);
    }

    // Update a Product
    public function update(): bool
    {
        $primaryKey = $this->getPrimaryKey();
        $query = "
            UPDATE {$this->table} 
            SET name = :name, description = :description, price = :price
            WHERE {$primaryKey} = :key_value
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = $this->sanitizeString($this->name);
        $this->description = $this->sanitizeString($this->description);
        $this->price = (float) $this->price;
        $keyValue = $this->sanitizeValue($this->getPrimaryKeyValue());

        // Bind Data
        $stmt->bindParam(":name", $this->name, PDO::PARAM_STR);
        $stmt->bindParam(":description", $this->description, PDO::PARAM_STR);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":key_value", $keyValue);

        return $this->executeStatement($stmt);
    }
}