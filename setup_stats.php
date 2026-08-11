<?php
require_once 'config/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_learning_time (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        log_date DATE NOT NULL,
        time_spent INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY(user_id, log_date),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Insert dummy data for past 7 days for testing (for users who have any activity)
    $stmt = $pdo->query("SELECT id FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach($users as $uid) {
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $random_minutes = rand(15, 120); // Random 15 mins to 2 hours
            $pdo->prepare("INSERT IGNORE INTO user_learning_time (user_id, log_date, time_spent) VALUES (?, ?, ?)")->execute([$uid, $date, $random_minutes]);
        }
    }

    echo "Stats table created and seeded.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
