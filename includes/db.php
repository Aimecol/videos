<?php
// Database connection
$host = 'localhost';
$dbname = 'video_platform';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create tables if they don't exist
function createTables($pdo) {
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        profile_image VARCHAR(255) DEFAULT 'default.jpg',
        is_admin BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    )");

    // Videos table
    $pdo->exec("CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        filename VARCHAR(255) NOT NULL,
        thumbnail VARCHAR(255) NOT NULL,
        duration INT NOT NULL,
        category VARCHAR(50),
        price DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Watched videos table
    $pdo->exec("CREATE TABLE IF NOT EXISTS watched_videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        video_id INT NOT NULL,
        watch_percentage INT DEFAULT 0,
        completed BOOLEAN DEFAULT FALSE,
        last_position INT DEFAULT 0,
        watched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
        UNIQUE KEY (user_id, video_id)
    )");

    // Categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT
    )");

    // Insert default categories if they don't exist
    $categories = [
        ['name' => 'Education', 'description' => 'Educational videos for learning new skills and knowledge'],
        ['name' => 'Entertainment', 'description' => 'Fun and entertaining videos to enjoy'],
        ['name' => 'Technology', 'description' => 'Videos about the latest technology trends and tutorials'],
        ['name' => 'Science', 'description' => 'Scientific explanations and discoveries'],
        ['name' => 'Health & Fitness', 'description' => 'Videos about health, fitness, and wellness'],
        ['name' => 'Business', 'description' => 'Business strategies, entrepreneurship, and career advice'],
        ['name' => 'Arts & Crafts', 'description' => 'Creative arts, DIY projects, and crafting tutorials'],
        ['name' => 'Music', 'description' => 'Music videos, tutorials, and performances'],
        ['name' => 'Cooking', 'description' => 'Cooking tutorials, recipes, and food-related content'],
        ['name' => 'Travel', 'description' => 'Travel guides, vlogs, and destination reviews']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
    foreach ($categories as $category) {
        $stmt->execute([$category['name'], $category['description']]);
    }

    // User sessions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_sessions (
        id VARCHAR(255) PRIMARY KEY,
        user_id INT NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // Create admins table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'editor',
        permissions TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        // Handle error silently
    }
    
    // Insert default admin user if it doesn't exist
    try {
        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin'");
        $stmt->execute();
        $adminExists = $stmt->fetch();
        
        if (!$adminExists) {
            // Create default admin user
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin, created_at) 
                                  VALUES (?, ?, ?, 1, NOW())");
            $stmt->execute(['admin', 'admin@gmail.com', $adminPassword]);
            
            $adminId = $pdo->lastInsertId();
            
            // Add to admins table
            $stmt = $pdo->prepare("INSERT INTO admins (user_id, role, created_at) 
                                  VALUES (?, 'administrator', NOW())");
            $stmt->execute([$adminId]);
        }
    } catch (PDOException $e) {
        // Handle error silently
    }
}

// Create tables
createTables($pdo);

// INSERT INTO `videos` (`id`, `title`, `description`, `filename`, `thumbnail`, `duration`, `category`, `price`, `created_at`) VALUES (NULL, 'Phoebe and the others from Freinds', 'Generate highly customizable CSS properties. Preview the results before copying them to your website.', 'Phoebe.mp4', 'Phoebe_thumb.png', '60', 'Business', '300', current_timestamp());
// INSERT INTO `videos` (`id`, `title`, `description`, `filename`, `thumbnail`, `duration`, `category`, `price`, `created_at`) VALUES (NULL, 'Fly high', 'Set up a modern, professional dashboard for VendorFlow—a payment management and fraud detection system for Rwandan sellers.', 'vid.mp4', 'vid_thumb.png', '45', 'Business', '200', current_timestamp());
?>
