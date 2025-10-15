<?php
class ListModel {
    private $conn;
    private $table = "lists";

    public $id;
    public $board_id;
    public $title;
    public $position;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (board_id, title, position) 
                  VALUES (:board_id, :title, :position)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":board_id", $this->board_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":position", $this->position);
        
        return $stmt->execute();
    }

    // Read by board
    public function readByBoard($board_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE board_id = :board_id 
                  ORDER BY position ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":board_id", $board_id);
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
                  SET title = :title, position = :position 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":position", $this->position);
        $stmt->bindParam(":id", $this->id);
        
        return $stmt->execute();
    }

    // Delete
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}
?>