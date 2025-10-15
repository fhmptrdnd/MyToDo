<?php
class Card {
    private $conn;
    private $table = "cards";

    public $id;
    public $list_id;
    public $title;
    public $description;
    public $image_path;
    public $position;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (list_id, title, description, image_path, position) 
                  VALUES (:list_id, :title, :description, :image_path, :position)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":list_id", $this->list_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":position", $this->position);
        
        return $stmt->execute();
    }

    // Read by list
    public function readByList($list_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE list_id = :list_id 
                  ORDER BY position ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":list_id", $list_id);
        $stmt->execute();
        return $stmt;
    }

    // Read single
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, description = :description, 
                      image_path = :image_path, position = :position 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }

    // Delete
    public function delete() {
        // Delete image file if exists
        if ($this->image_path && file_exists($this->image_path)) {
            unlink($this->image_path);
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}
?>