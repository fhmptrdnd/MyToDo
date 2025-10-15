<?php
// File untuk testing koneksi database dan debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Database Connection</h2>";

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "<p style='color: green;'>✓ Database connected successfully!</p>";
    
    // Test query
    try {
        $query = "SELECT COUNT(*) as count FROM boards";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p>✓ Number of boards: " . $result['count'] . "</p>";
        
        $query = "SELECT COUNT(*) as count FROM lists";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p>✓ Number of lists: " . $result['count'] . "</p>";
        
        $query = "SELECT COUNT(*) as count FROM cards";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p>✓ Number of cards: " . $result['count'] . "</p>";
        
        echo "<hr>";
        echo "<h3>All Systems Ready!</h3>";
        echo "<p><a href='index.php'>Go to Application →</a></p>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
}
?>