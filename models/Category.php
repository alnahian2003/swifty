<?php

declare(strict_types=1);

/**
 * Legacy Category model - maintained for backward compatibility
 * For new code, use Swifty\Models\Category instead
 * 
 * @deprecated Use Swifty\Models\Category instead
 */
class Category
{
    // DB Related
    private PDO $conn;
    private string $table = "categories";

    // Category Properties
    public ?int $id = null;
    public ?string $name = null;
    public ?string $title = null; // For backward compatibility

    // Construct with Database
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Get All Categories
    public function read(): PDOStatement
    {
        $query = "
            SELECT * 
            FROM {$this->table} 
            ORDER BY id DESC
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Get a Single Category
    public function single(): bool
    {
        $query = "
            SELECT * 
            FROM {$this->table} 
            WHERE id = ?
            LIMIT 1
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Get the category
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($category) {
                $this->id = $category["id"];
                $this->name = $category["name"];
                $this->title = $category["name"]; // For backward compatibility
                
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

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
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

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete a Category
    public function delete(): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = :id";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = $this->sanitizeInt($this->id);
        
        // Bind Data 
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
    private function sanitizeString(?string $input): string
    {
        if ($input === null) {
            return '';
        }
        
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize integer input
     */
    private function sanitizeInt($input): int
    {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
}
