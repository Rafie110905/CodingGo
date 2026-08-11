<?php
require_once 'config/db.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS championships (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        start_date DATETIME,
        end_date DATETIME,
        status VARCHAR(50) DEFAULT 'upcoming',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS championship_participants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        championship_id INT NOT NULL,
        user_id INT NOT NULL,
        xp_earned INT DEFAULT 0,
        joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (championship_id) REFERENCES championships(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY(championship_id, user_id)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS championship_challenges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        championship_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        correct_answer VARCHAR(255),
        xp_reward INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (championship_id) REFERENCES championships(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS championship_completed_challenges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        challenge_id INT NOT NULL,
        user_id INT NOT NULL,
        completed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (challenge_id) REFERENCES championship_challenges(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY(challenge_id, user_id)
    )");

    echo "Championship tables created successfully.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
