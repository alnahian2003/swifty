<?php

declare(strict_types=1);

/**
 * Legacy Post model - maintained for backward compatibility
 * For new code, use Swifty\Models\Post instead
 * 
 * @deprecated Use Swifty\Models\Post instead
 */
class Post
{
    // DB Related
    private PDO $conn;
    private string $table = "posts";

    // Post Properties
    public ?int $id = null;
    public ?int $categoryId = null;
    public ?string $categoryName = null;
    public ?string $title = null;
    public ?string $body = null;
    public ?string $author = null;
    public ?string $created_at = null;
    public ?string $createdAt = null; // Added for consistency

    // Construct with Database
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // Get All Posts
    public function read(): PDOStatement
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
            FROM {$this->table} p
            LEFT JOIN 
                categories as c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Get a Single Post
    public function get(int $id): bool
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
            FROM {$this->table} p
            LEFT JOIN 
                categories as c ON p.category_id = c.id
            WHERE p.id = ?
            LIMIT 1
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Get the post
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($post) {
                $this->id = $post["id"];
                $this->title = $post["title"];
                $this->categoryId = $post["category_id"];
                $this->categoryName = $post["category_name"];
                $this->body = $post["body"];
                $this->author = $post["author"];
                $this->createdAt = $post["created_at"];
                $this->created_at = $post["created_at"]; // Backward compatibility

                return true;
            }
        }

        return false;
    }

    // Create a Post
    public function create(): bool
    {
        $query = "
            INSERT INTO {$this->table} 
            SET 
                title = :title,
                category_id = :category_id, 
                body = :body, 
                author = :author
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data - improved security
        $this->title = $this->sanitizeString($this->title);
        $this->categoryId = $this->sanitizeInt($this->categoryId);
        $this->author = $this->sanitizeString($this->author);
        $this->body = $this->sanitizeString($this->body);

        // Bind Data
        $stmt->bindParam(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindParam(":category_id", $this->categoryId, PDO::PARAM_INT);
        $stmt->bindParam(":body", $this->body, PDO::PARAM_STR);
        $stmt->bindParam(":author", $this->author, PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }


    // Update a Post
    public function update(): bool
    {
        $query = "
            UPDATE {$this->table} 
            SET 
                title = :title,
                category_id = :category_id, 
                body = :body, 
                author = :author 
            WHERE id = :id
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data - improved security
        $this->id = $this->sanitizeInt($this->id);
        $this->title = $this->sanitizeString($this->title);
        $this->categoryId = $this->sanitizeInt($this->categoryId);
        $this->author = $this->sanitizeString($this->author);
        $this->body = $this->sanitizeString($this->body);

        // Bind Data
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindParam(":category_id", $this->categoryId, PDO::PARAM_INT);
        $stmt->bindParam(":body", $this->body, PDO::PARAM_STR);
        $stmt->bindParam(":author", $this->author, PDO::PARAM_STR);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete a Post
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
