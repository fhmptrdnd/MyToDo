<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Board.php';

$database = new Database();
$db = $database->getConnection();
$board = new Board($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'update') {
        $board->id = $_POST['id'];
        $board->title = $_POST['title'];
        $board->description = isset($_POST['description']) ? $_POST['description'] : '';
        $board->color = $_POST['color'];
        
        if ($board->update()) {
            header("Location: ../index.php");
            exit();
        } else {
            echo "Failed to update board";
        }
    }
}

// Handle delete via GET
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $board->id = $_GET['id'];
    
    if ($board->delete()) {
        header("Location: ../index.php");
        exit();
    } else {
        echo "Failed to delete board";
    }
}
?>