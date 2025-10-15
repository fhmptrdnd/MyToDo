<?php
require_once 'config/database.php';
require_once 'models/Board.php';

$database = new Database();
$db = $database->getConnection();

$board = new Board($db);
$stmt = $board->read();
$boards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Untuk create boards handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_board') {
    $board->title = $_POST['title'];
    $board->description = isset($_POST['description']) ? $_POST['description'] : '';
    $board->color = $_POST['color'];
    
    if ($board->create()) {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyToDo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <h1 class="logo">MyToDo</h1>
            <button class="btn-primary" onclick="showCreateBoardModal()">+ New Board</button>
        </div>
    </nav>

    <div class="container main-content">
        <h2 class="page-title">My Boards</h2>
        
        <div class="boards-grid">
            <?php foreach ($boards as $b): ?>
            <div class="board-card" style="border-left: 4px solid <?= htmlspecialchars($b['color']) ?>">
                <div class="board-card-header">
                    <h3><?= htmlspecialchars($b['title']) ?></h3>
                    <div class="board-actions">
                        <a href="board.php?id=<?= $b['id'] ?>" class="btn-icon" title="Open">◉</a>
                        <button onclick="editBoard(<?= $b['id'] ?>, '<?= htmlspecialchars(addslashes($b['title'])) ?>', '<?= htmlspecialchars(addslashes($b['description'])) ?>', '<?= htmlspecialchars($b['color']) ?>')" class="btn-icon" title="Edit">❖</button>
                        <button onclick="deleteBoard(<?= $b['id'] ?>)" class="btn-icon" title="Delete">☒</button>
                    </div>
                </div>
                <p class="board-description"><?= htmlspecialchars($b['description']) ?></p>
                <div class="board-footer">
                    <small>Created: <?= date('d M Y', strtotime($b['created_at'])) ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="createBoardModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createBoardModal')">&times;</span>
            <h2>Create New Board</h2>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="create_board">
                <div class="form-group">
                    <label>Board Title</label>
                    <input type="text" name="title" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Color Theme</label>
                    <div class="color-picker">
                        <input type="radio" name="color" value="#FFB5E8" id="color1" checked>
                        <label for="color1" style="background: #FFB5E8"></label>
                        
                        <input type="radio" name="color" value="#B5F8FF" id="color2">
                        <label for="color2" style="background: #B5F8FF"></label>
                        
                        <input type="radio" name="color" value="#FFFBB5" id="color3">
                        <label for="color3" style="background: #FFFBB5"></label>
                        
                        <input type="radio" name="color" value="#C7FFB5" id="color4">
                        <label for="color4" style="background: #C7FFB5"></label>
                        
                        <input type="radio" name="color" value="#E0B5FF" id="color5">
                        <label for="color5" style="background: #E0B5FF"></label>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Create Board</button>
            </form>
        </div>
    </div>

    <div id="editBoardModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editBoardModal')">&times;</span>
            <h2>Edit Board</h2>
            <form method="POST" action="controllers/board_controller.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_board_id">
                <div class="form-group">
                    <label>Board Title</label>
                    <input type="text" name="title" id="edit_board_title" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_board_description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Color Theme</label>
                    <div class="color-picker">
                        <input type="radio" name="color" value="#FFB5E8" id="edit_color1">
                        <label for="edit_color1" style="background: #FFB5E8"></label>
                        
                        <input type="radio" name="color" value="#B5F8FF" id="edit_color2">
                        <label for="edit_color2" style="background: #B5F8FF"></label>
                        
                        <input type="radio" name="color" value="#FFFBB5" id="edit_color3">
                        <label for="edit_color3" style="background: #FFFBB5"></label>
                        
                        <input type="radio" name="color" value="#C7FFB5" id="edit_color4">
                        <label for="edit_color4" style="background: #C7FFB5"></label>
                        
                        <input type="radio" name="color" value="#E0B5FF" id="edit_color5">
                        <label for="edit_color5" style="background: #E0B5FF"></label>
                    </div>
                </div>
                <button type="submit" class="btn-primary">Update Board</button>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>