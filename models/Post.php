<?php
class Post
{
    // DB Related
    private $conn;
    private $table = "posts";

    // Post Properties
    public $id;
    public $categoryId;
    public $categoryName;
    public $title;
    public $body;
    public $author;
    public $created_at;

    // Construct with Database
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get All Posts
    public function read()
    {
        $query = "SELECT
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
    public function single()
    {
        $query = "SELECT
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
        LIMIT 0,1
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            // Get the post
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->title = $post["title"];
            $this->categoryId = $post["category_id"];
            $this->categoryName = $post["category_name"];
            $this->body = $post["body"];
            $this->author = $post["author"];
            $this->createdAt = $post["created_at"];

            return true;
        } else {
            printf("Database Error: %s\n", $stmt->error);
            return false;
        }
    }

    // Create a Post
    public function create()
    {
        $query = "INSERT INTO {$this->table} 
        SET 
         title = :title,
         category_id= :category_id, 
         body= :body, 
         author= :author";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->title = htmlspecialchars(strip_tags(trim($this->title)));
        $this->categoryId = htmlspecialchars(strip_tags(trim($this->categoryId)));
        $this->author = htmlspecialchars(strip_tags(trim($this->author)));
        $this->body = htmlspecialchars(strip_tags(trim($this->body)));

        // Bind Data
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":category_id", $this->categoryId);
        $stmt->bindParam(":body", $this->body);
        $stmt->bindParam(":author", $this->author);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Database Error: %s\n", $stmt->error);
            return false;
        }
    }


    // Update a Post
    public function update()
    {
        $query = "UPDATE {$this->table} 
        SET 
         title = :title,
         category_id= :category_id, 
         body= :body, 
         author= :author 
         WHERE id = :id";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = htmlspecialchars(strip_tags(trim($this->id)));
        $this->title = htmlspecialchars(strip_tags(trim($this->title)));
        $this->categoryId = htmlspecialchars(strip_tags(trim($this->categoryId)));
        $this->author = htmlspecialchars(strip_tags(trim($this->author)));
        $this->body = htmlspecialchars(strip_tags(trim($this->body)));

        // Bind Data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":category_id", $this->categoryId);
        $stmt->bindParam(":body", $this->body);
        $stmt->bindParam(":author", $this->author);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Database Error: %s\n", $stmt->error);
            return false;
        }
    }

    // Delete a Post
    public function delete()
    {
        $query = "DELETE FROM {$this->table} WHERE id=:id";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = htmlspecialchars(strip_tags(trim($this->id)));
        // Bind Data 
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Database Error: %s\n", $stmt->error);
            return false;
        }
    }
}
