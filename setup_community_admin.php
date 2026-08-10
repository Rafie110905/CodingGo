<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE forum_posts ADD COLUMN is_official TINYINT(1) DEFAULT 0");
    echo "Column is_official added successfully.";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
