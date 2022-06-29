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
}
