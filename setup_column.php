<?php
require_once 'config/db.php';
try {
    $pdo->exec("ALTER TABLE materials ADD COLUMN attachment_file VARCHAR(255) NULL");
    echo "Column added.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
