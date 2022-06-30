<?php
class Category
{
    // DB Related
    private $conn;
    private $table = "categories";

    // Post Properties
    public $id;
    public $name;

    // Construct with Database
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get All Categories
    public function read()
    {
        $query = "SELECT *
        FROM {$this->table} 
        ORDER BY id DESC
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    // Get a Single Category
    public function single()
    {
        $query = "SELECT *
        FROM {$this->table} 
        WHERE id = ?
        LIMIT 0,1
        ";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            // Get the category
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->title = $post["id"];
            return true;
        } else {
            printf("Database Error: %s\n", $stmt->error);
            return false;
        }
    }

    // Create a Category
    public function create()
    {
        $query = "INSERT INTO {$this->table} 
        SET 
         name = :name";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->name = htmlspecialchars(strip_tags(trim($this->name)));

        // Bind Data
        $stmt->bindParam(":name", $this->name);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Database Error: %s\n", $stmt->error);
            return false;
        }
    }


    // Update a Category
    public function update()
    {
        $query = "UPDATE {$this->table} 
        SET 
         name = :name 
         WHERE id = :id";

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = htmlspecialchars(strip_tags(trim($this->id)));
        $this->name = htmlspecialchars(strip_tags(trim($this->name)));

        // Bind Data
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Database Error: %s\n", $stmt->error);
            return false;
        }
    }

    // Delete a Category
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
