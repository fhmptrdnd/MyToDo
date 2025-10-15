<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ListModel.php';

$database = new Database();
$db = $database->getConnection();
$list = new ListModel($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $board_id = isset($_POST['board_id']) ? $_POST['board_id'] : '';
    
    if ($action === 'create') {
        $list->board_id = $board_id;
        $list->title = $_POST['title'];
        $list->position = 0;
        
        if ($list->create()) {
            header("Location: ../board.php?id=" . $board_id);
            exit();
        } else {
            echo "Failed to create list";
        }
    }
    
    if ($action === 'update') {
        $list->id = $_POST['id'];
        $list->title = $_POST['title'];
        $list->position = 0;
        
        if ($list->update()) {
            header("Location: ../board.php?id=" . $board_id);
            exit();
        } else {
            echo "Failed to update list";
        }
    }
}

// Handle delete via GET
if (isset($_GET['delete']) && isset($_GET['board_id'])) {
    $list->id = $_GET['delete'];
    $board_id = $_GET['board_id'];
    
    if ($list->delete()) {
        header("Location: ../board.php?id=" . $board_id);
        exit();
    } else {
        echo "Failed to delete list";
    }
}
?>