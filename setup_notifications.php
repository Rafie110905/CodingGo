<?php
require_once 'config/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type VARCHAR(50) DEFAULT 'system',
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        link_url VARCHAR(255) DEFAULT '#',
        is_read TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    // Insert a dummy notification for testing for all users
    $stmt = $pdo->query("SELECT id FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach($users as $uid) {
        $pdo->prepare("INSERT INTO user_notifications (user_id, type, title, message, link_url) VALUES (?, 'system', 'Selamat Datang!', 'Selamat datang di CodingGo. Ayo mulai petualangan coding-mu!', 'index.php?page=dashboard')")->execute([$uid]);
    }

    echo "Notifications table created and seeded.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
