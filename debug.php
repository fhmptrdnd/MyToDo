<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Page</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        h2 { border-bottom: 2px solid #FFB5E8; padding-bottom: 10px; }
        button { background: #FFB5E8; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #FF9ECE; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Debug & Testing Page</h1>

    <!-- Test 1: Database Connection -->
    <div class="box">
        <h2>1. Database Connection Test</h2>
        <?php
        require_once __DIR__ . '/config/database.php';
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                echo "<p class='success'>✓ Database connected successfully!</p>";
                echo "<p>Connection Type: " . get_class($db) . "</p>";
            } else {
                echo "<p class='error'>✗ Database connection failed!</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 2: Check Tables -->
    <div class="box">
        <h2>2. Database Tables Check</h2>
        <?php
        if ($db) {
            $tables = ['boards', 'lists', 'cards'];
            foreach ($tables as $table) {
                try {
                    $query = "SELECT COUNT(*) as count FROM $table";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    echo "<p class='success'>✓ Table '$table': {$result['count']} records</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>✗ Table '$table': " . $e->getMessage() . "</p>";
                }
            }
        }
        ?>
    </div>

    <!-- Test 3: Check Folders -->
    <div class="box">
        <h2>3. Folder Structure Check</h2>
        <?php
        $folders = [
            'config' => __DIR__ . '/config',
            'models' => __DIR__ . '/models',
            'controllers' => __DIR__ . '/controllers',
            'assets' => __DIR__ . '/assets',
            'assets/css' => __DIR__ . '/assets/css',
            'assets/js' => __DIR__ . '/assets/js',
            'uploads' => __DIR__ . '/uploads'
        ];
        
        foreach ($folders as $name => $path) {
            if (file_exists($path)) {
                $writable = is_writable($path) ? '(writable)' : '(not writable)';
                echo "<p class='success'>✓ Folder '$name' exists $writable</p>";
            } else {
                echo "<p class='error'>✗ Folder '$name' does NOT exist</p>";
                
                // Try to create uploads folder
                if ($name == 'uploads') {
                    if (mkdir($path, 0777, true)) {
                        chmod($path, 0777);
                        echo "<p class='success'>✓ Created 'uploads' folder successfully!</p>";
                    } else {
                        echo "<p class='error'>✗ Failed to create 'uploads' folder</p>";
                    }
                }
            }
        }
        ?>
    </div>

    <!-- Test 4: Check Files -->
    <div class="box">
        <h2>4. Required Files Check</h2>
        <?php
        $files = [
            'index.php' => __DIR__ . '/index.php',
            'board.php' => __DIR__ . '/board.php',
            'config/database.php' => __DIR__ . '/config/database.php',
            'models/Board.php' => __DIR__ . '/models/Board.php',
            'models/ListModel.php' => __DIR__ . '/models/ListModel.php',
            'models/Card.php' => __DIR__ . '/models/Card.php',
            'controllers/board_controller.php' => __DIR__ . '/controllers/board_controller.php',
            'controllers/list_controller.php' => __DIR__ . '/controllers/list_controller.php',
            'controllers/card_controller.php' => __DIR__ . '/controllers/card_controller.php',
            'assets/css/style.css' => __DIR__ . '/assets/css/style.css',
            'assets/js/script.js' => __DIR__ . '/assets/js/script.js'
        ];
        
        foreach ($files as $name => $path) {
            if (file_exists($path)) {
                $size = filesize($path);
                echo "<p class='success'>✓ File '$name' exists (" . number_format($size) . " bytes)</p>";
            } else {
                echo "<p class='error'>✗ File '$name' does NOT exist</p>";
            }
        }
        ?>
    </div>

    <!-- Test 5: JavaScript Test -->
    <div class="box">
        <h2>5. JavaScript Functions Test</h2>
        <button onclick="testJS()">Test Alert</button>
        <button onclick="testModal()">Test Modal Function</button>
        <div id="jsResult"></div>
        
        <script>
        function testJS() {
            alert('JavaScript is working! ✓');
            document.getElementById('jsResult').innerHTML = '<p class="success">✓ JavaScript Alert works!</p>';
        }
        
        function testModal() {
            document.getElementById('jsResult').innerHTML = '<p class="success">✓ JavaScript function can be called!</p>';
        }
        
        // Check if script.js is loaded
        if (typeof showCreateBoardModal === 'function') {
            document.getElementById('jsResult').innerHTML = '<p class="success">✓ script.js is loaded correctly!</p>';
        } else {
            document.getElementById('jsResult').innerHTML = '<p class="warning">⚠ script.js might not be loaded</p>';
        }
        </script>
    </div>

    <!-- Test 6: Model Classes -->
    <div class="box">
        <h2>6. Model Classes Test</h2>
        <?php
        require_once __DIR__ . '/models/Board.php';
        require_once __DIR__ . '/models/ListModel.php';
        require_once __DIR__ . '/models/Card.php';
        
        try {
            $board = new Board($db);
            echo "<p class='success'>✓ Board class instantiated</p>";
            
            $list = new ListModel($db);
            echo "<p class='success'>✓ ListModel class instantiated</p>";
            
            $card = new Card($db);
            echo "<p class='success'>✓ Card class instantiated</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- Test 7: PHP Info -->
    <div class="box">
        <h2>7. PHP Configuration</h2>
        <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
        <p><strong>PDO Support:</strong> <?= extension_loaded('pdo') ? '✓ Yes' : '✗ No' ?></p>
        <p><strong>PDO MySQL:</strong> <?= extension_loaded('pdo_mysql') ? '✓ Yes' : '✗ No' ?></p>
        <p><strong>File Uploads:</strong> <?= ini_get('file_uploads') ? '✓ Enabled' : '✗ Disabled' ?></p>
        <p><strong>Max Upload Size:</strong> <?= ini_get('upload_max_filesize') ?></p>
        <p><strong>Max POST Size:</strong> <?= ini_get('post_max_size') ?></p>
    </div>

    <div class="box">
        <h2>✅ Quick Actions</h2>
        <a href="index.php"><button>Go to Application</button></a>
        <a href="test_connection.php"><button>Simple Connection Test</button></a>
        <button onclick="location.reload()">Refresh Debug Page</button>
    </div>

</body>
</html>