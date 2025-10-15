<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Board.php';
require_once __DIR__ . '/models/ListModel.php';
require_once __DIR__ . '/models/Card.php';

$database = new Database();
$db = $database->getConnection();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Board ID not specified. <a href="index.php">Go back to boards</a>');
}

$board_id = $_GET['id'];

$board = new Board($db);
$board->id = $board_id;
$boardData = $board->readOne();

if (!$boardData) {
    die('Board not found. <a href="index.php">Go back to boards</a>');
}

$listModel = new ListModel($db);
$stmt = $listModel->readByBoard($board_id);
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

$card = new Card($db);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($boardData['title']) ?>MyToDo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div>
                <a href="index.php" class="back-link">Go Back</a>
                <h1 class="board-title"><?= htmlspecialchars($boardData['title']) ?></h1>
            </div>
            <button class="btn-primary" onclick="showCreateListModal()">+ Add List</button>
        </div>
    </nav>

    <div class="board-container">
        <div class="lists-wrapper">
            <?php foreach ($lists as $list): ?>
            <div class="list-column" data-list-id="<?= $list['id'] ?>">
                <div class="list-header">
                    <h3><?= htmlspecialchars($list['title']) ?></h3>
                    <div class="list-actions">
                        <button onclick="editList(<?= $list['id'] ?>, '<?= htmlspecialchars(addslashes($list['title'])) ?>')" class="btn-icon-small">❖</button>
                        <button onclick="deleteList(<?= $list['id'] ?>)" class="btn-icon-small">☒</button>
                    </div>
                </div>
                
                <div class="cards-container">
                    <?php
                    $stmtCards = $card->readByList($list['id']);
                    $cards = $stmtCards->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($cards as $c):
                    ?>
                    <div class="card" data-card-id="<?= $c['id'] ?>">
                        <?php if ($c['image_path']): ?>
                        <img src="<?= htmlspecialchars($c['image_path']) ?>" alt="Card image" class="card-image">
                        <?php endif; ?>
                        <h4 class="card-title"><?= htmlspecialchars($c['title']) ?></h4>
                        <?php if ($c['description']): ?>
                        <p class="card-description"><?= htmlspecialchars($c['description']) ?></p>
                        <?php endif; ?>
                        <div class="card-actions">
                            <button class="edit-card-btn btn-text" 
                                data-card-id="<?= $c['id'] ?>" 
                                data-list-id="<?= $list['id'] ?>"
                                data-title="<?= htmlspecialchars($c['title']) ?>"
                                data-description="<?= htmlspecialchars($c['description']) ?>"
                                data-image-path="<?= htmlspecialchars($c['image_path']) ?>">
                                Edit
                            </button>
                            <button onclick="deleteCard(<?= $c['id'] ?>)" class="btn-text danger">Delete</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <button class="btn-add-card" onclick="showCreateCardModal(<?= $list['id'] ?>)">+ Add Card</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Create List Modal -->
    <div id="createListModal" class="modal">
        <div class="modal-content modal-small">
            <span class="close" onclick="closeModal('createListModal')">&times;</span>
            <h2>Create New List</h2>
            <form method="POST" action="controllers/list_controller.php">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="board_id" value="<?= $board_id ?>">
                <div class="form-group">
                    <label>List Title</label>
                    <input type="text" name="title" required class="form-control">
                </div>
                <button type="submit" class="btn-primary">Create List</button>
            </form>
        </div>
    </div>

    <!-- Edit List Modal -->
    <div id="editListModal" class="modal">
        <div class="modal-content modal-small">
            <span class="close" onclick="closeModal('editListModal')">&times;</span>
            <h2>Edit List</h2>
            <form method="POST" action="controllers/list_controller.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_list_id">
                <input type="hidden" name="board_id" value="<?= $board_id ?>">
                <div class="form-group">
                    <label>List Title</label>
                    <input type="text" name="title" id="edit_list_title" required class="form-control">
                </div>
                <button type="submit" class="btn-primary">Update List</button>
            </form>
        </div>
    </div>

    <!-- Create Card Modal -->
    <div id="createCardModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('createCardModal')">&times;</span>
            <h2>Create New Card</h2>
            <form method="POST" action="controllers/card_controller.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="list_id" id="create_card_list_id">
                <input type="hidden" name="board_id" value="<?= $board_id ?>">
                <div class="form-group">
                    <label>Card Title</label>
                    <input type="text" name="title" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Description (Optional)</label>
                    <textarea name="description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Upload Image (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
                    <small>Max 2MB. Format: JPG, PNG, GIF</small>
                </div>
                <button type="submit" class="btn-primary">Create Card</button>
            </form>
        </div>
    </div>

    <!-- Edit Card Modal -->
    <div id="editCardModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editCardModal')">&times;</span>
            <h2>Edit Card</h2>
            <form method="POST" action="controllers/card_controller.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_card_id">
                <input type="hidden" name="list_id" id="edit_card_list_id">
                <input type="hidden" name="board_id" value="<?= $board_id ?>">
                <input type="hidden" name="old_image" id="edit_card_old_image">
                <div class="form-group">
                    <label>Card Title</label>
                    <input type="text" name="title" id="edit_card_title" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Description (Optional)</label>
                    <textarea name="description" id="edit_card_description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label>Upload New Image (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
                    <small>Leave empty to keep current image. Max 2MB.</small>
                    <div id="current_image_preview" style="margin-top: 10px;"></div>
                </div>
                <button type="submit" class="btn-primary">Update Card</button>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>