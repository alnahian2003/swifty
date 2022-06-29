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
        $stmt->execute();

        // Get the post
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->title = $post["title"];
        $this->categoryId = $post["category_id"];
        $this->categoryName = $post["category_name"];
        $this->body = $post["body"];
        $this->author = $post["author"];
        $this->createdAt = $post["created_at"];
    }
}
