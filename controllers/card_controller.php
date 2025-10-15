<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Card.php';

$database = new Database();
$db = $database->getConnection();
$card = new Card($db);

// Handle image upload
function handleImageUpload($file) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($filetype, $allowed)) {
            return null;
        }
        
        if ($file['size'] > 2097152) { // 2MB
            return null;
        }
        
        $upload_dir = __DIR__ . '/../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $new_filename = uniqid() . '_' . time() . '.' . $filetype;
        $destination = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/' . $new_filename;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $board_id = isset($_POST['board_id']) ? $_POST['board_id'] : '';
    
    if ($action === 'create') {
        $card->list_id = $_POST['list_id'];
        $card->title = $_POST['title'];
        $card->description = isset($_POST['description']) ? $_POST['description'] : '';
        $card->position = 0;
        
        // Handle image upload
        $card->image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $card->image_path = handleImageUpload($_FILES['image']);
        }
        
        if ($card->create()) {
            header("Location: ../board.php?id=" . $board_id);
            exit();
        } else {
            echo "Failed to create card";
        }
    }
    
    if ($action === 'update') {
        $card->id = $_POST['id'];
        $card->list_id = $_POST['list_id'];
        $card->title = $_POST['title'];
        $card->description = isset($_POST['description']) ? $_POST['description'] : '';
        $card->position = 0;
        
        // Handle image upload
        $card->image_path = isset($_POST['old_image']) ? $_POST['old_image'] : '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $new_image = handleImageUpload($_FILES['image']);
            if ($new_image) {
                // Delete old image
                if ($card->image_path && file_exists(__DIR__ . '/../' . $card->image_path)) {
                    unlink(__DIR__ . '/../' . $card->image_path);
                }
                $card->image_path = $new_image;
            }
        }
        
        if ($card->update()) {
            header("Location: ../board.php?id=" . $board_id);
            exit();
        } else {
            echo "Failed to update card";
        }
    }
}

// Handle delete via GET
if (isset($_GET['delete']) && isset($_GET['board_id'])) {
    $card->id = $_GET['delete'];
    $board_id = $_GET['board_id'];
    
    // Get card data to delete image
    $cardData = $card->readOne();
    if ($cardData && $cardData['image_path']) {
        $card->image_path = $cardData['image_path'];
        if (file_exists(__DIR__ . '/../' . $card->image_path)) {
            unlink(__DIR__ . '/../' . $card->image_path);
        }
    }
    
    if ($card->delete()) {
        header("Location: ../board.php?id=" . $board_id);
        exit();
    } else {
        echo "Failed to delete card";
    }
}
?>