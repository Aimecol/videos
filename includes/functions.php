<?php
require_once 'db.php';
require_once 'auth.php'; // Make sure auth.php is included

// Get all videos
function getAllVideos() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Get video by ID
function getVideoById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Get videos by category
function getVideosByCategory($category) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM videos WHERE category = ? ORDER BY created_at DESC");
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Get user's watched videos
function getUserWatchedVideos($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT v.*, w.watch_percentage, w.completed, w.last_position, w.watched_at
            FROM videos v
            JOIN watched_videos w ON v.id = w.video_id
            WHERE w.user_id = ?
            ORDER BY w.watched_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Update watch status
function updateWatchStatus($userId, $videoId, $percentage, $position, $completed = false) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO watched_videos (user_id, video_id, watch_percentage, last_position, completed, watched_at)
            VALUES (?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            watch_percentage = ?, last_position = ?, completed = ?, watched_at = NOW()
        ");
        $stmt->execute([
            $userId, $videoId, $percentage, $position, $completed,
            $percentage, $position, $completed
        ]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Get watch status for a video
function getWatchStatus($userId, $videoId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM watched_videos
            WHERE user_id = ? AND video_id = ?
        ");
        $stmt->execute([$userId, $videoId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Format duration from seconds to MM:SS
function formatDuration($seconds) {
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $seconds);
}

// Get all categories
function getAllCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get total number of videos in the database
 * 
 * @param int $limit Number of videos to return
 * @return int Total number of videos
 */
function getTotalVideos() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM videos");
        $data = $stmt->fetch();
        return $data['total'];
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Get total number of users in the database
 * 
 * @return int Total number of users
 */
function getTotalUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
        $data = $stmt->fetch();
        return $data['total'];
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Get total number of categories in the database
 * 
 * @return int Total number of categories
 */
function getTotalCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
        $data = $stmt->fetch();
        return $data['total'];
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Get recent videos with limit
 * 
 * @param int $limit Number of videos to return
 * @return array Array of recent videos
 */
function getRecentVideos($limit = 5) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT v.*, c.name as category 
                              FROM videos v 
                              LEFT JOIN categories c ON v.category_id = c.id 
                              ORDER BY v.created_at DESC 
                              LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get recent users with limit
 * 
 * @param int $limit Number of users to return
 * @return array Array of recent users
 */
function getRecentUsers($limit = 5) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        $users = $stmt->fetchAll();
        
        // Set default profile image if none exists
        foreach ($users as &$user) {
            if (empty($user['profile_image'])) {
                $user['profile_image'] = 'default.jpg';
            }
        }
        
        return $users;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get total views across all videos
 * 
 * @return int Total number of views
 */
function getTotalViews() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT SUM(views) as total_views FROM videos");
        $data = $stmt->fetch();
        return $data['total_views'] ? $data['total_views'] : 0;
    } catch (PDOException $e) {
        return 0;
    }
}

// Remove the getCurrentUser() function since it's already defined in auth.php

/**
 * Get users with pagination, search and role filtering
 * 
 * @param int $limit Number of users per page
 * @param int $offset Pagination offset
 * @param string $search Search term
 * @param string $roleFilter Role filter (admin or regular)
 * @return array Array of users
 */
function getUsers($limit = 10, $offset = 0, $search = '', $roleFilter = '') {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (username LIKE ? OR email LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if ($roleFilter === 'admin') {
            $sql .= " AND is_admin = 1";
        } elseif ($roleFilter === 'regular') {
            $sql .= " AND is_admin = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $users = $stmt->fetchAll();
        
        // Set default profile image if none exists
        foreach ($users as &$user) {
            if (empty($user['profile_image'])) {
                $user['profile_image'] = 'default.jpg';
            }
        }
        
        return $users;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get total number of users with search and role filtering
 * 
 * @param string $search Search term
 * @param string $roleFilter Role filter (admin or regular)
 * @return int Total number of users
 */
function getTotalUsersCount($search = '', $roleFilter = '') {
    global $pdo;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (username LIKE ? OR email LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if ($roleFilter === 'admin') {
            $sql .= " AND is_admin = 1";
        } elseif ($roleFilter === 'regular') {
            $sql .= " AND is_admin = 0";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetch();
        
        return $data['total'];
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Get user by ID
 * 
 * @param int $userId User ID
 * @return array|null User data or null if not found
 */
function getUserById($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return null;
        }
        
        // Set default profile image if none exists
        if (empty($user['profile_image'])) {
            $user['profile_image'] = 'default.jpg';
        }
        
        return $user;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Delete a user
 * 
 * @param int $userId User ID
 * @return bool True if successful, false otherwise
 */
function deleteUser($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Toggle admin status for a user
 * 
 * @param int $userId User ID
 * @return bool True if successful, false otherwise
 */
function toggleAdminStatus($userId) {
    global $pdo;
    
    try {
        // First get current status
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        // Toggle status
        $newStatus = $user['is_admin'] ? 0 : 1;
        
        $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);
        
        // If making admin, add to admins table if not already there
        if ($newStatus == 1) {
            $stmt = $pdo->prepare("SELECT id FROM admins WHERE user_id = ?");
            $stmt->execute([$userId]);
            $adminExists = $stmt->fetch();
            
            if (!$adminExists) {
                $stmt = $pdo->prepare("INSERT INTO admins (user_id, role, created_at) VALUES (?, 'editor', NOW())");
                $stmt->execute([$userId]);
            }
        }
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Get videos with pagination, search and category filtering
 * 
 * @param int $limit Number of videos per page
 * @param int $offset Pagination offset
 * @param string $search Search term
 * @param string $categoryFilter Category filter
 * @return array Array of videos
 */
function getVideos($limit = 10, $offset = 0, $search = '', $categoryFilter = '') {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM videos WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($categoryFilter)) {
            $sql .= " AND category = ?";
            $params[] = $categoryFilter;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get total number of videos with search and category filtering
 * 
 * @param string $search Search term
 * @param string $categoryFilter Category filter
 * @return int Total number of videos
 */
function getTotalVideosCount($search = '', $categoryFilter = '') {
    global $pdo;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM videos WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($categoryFilter)) {
            $sql .= " AND category = ?";
            $params[] = $categoryFilter;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetch();
        
        return $data['total'];
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Add a new video
 * 
 * @param string $title Video title
 * @param string $description Video description
 * @param string $category Video category
 * @param array $videoFile Uploaded video file ($_FILES['video_file'])
 * @param array $thumbnailFile Uploaded thumbnail file ($_FILES['thumbnail'])
 * @return bool True if successful, false otherwise
 */
function addVideo($title, $description, $category, $videoFile, $thumbnailFile) {
    global $pdo;
    
    // Validate inputs
    if (empty($title) || empty($description) || empty($category)) {
        return false;
    }
    
    // Check if files were uploaded
    if ($videoFile['error'] !== UPLOAD_ERR_OK || $thumbnailFile['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Generate unique filenames
    $videoFilename = uniqid() . '_' . basename($videoFile['name']);
    $thumbnailFilename = uniqid() . '_' . basename($thumbnailFile['name']);
    
    // Define upload paths
    $videoPath = '../assets/videos/' . $videoFilename;
    $thumbnailPath = '../assets/images/thumbnails/' . $thumbnailFilename;
    
    // Create directories if they don't exist
    if (!file_exists('../assets/videos/')) {
        mkdir('../assets/videos/', 0777, true);
    }
    
    if (!file_exists('../assets/images/thumbnails/')) {
        mkdir('../assets/images/thumbnails/', 0777, true);
    }
    
    // Move uploaded files
    if (!move_uploaded_file($videoFile['tmp_name'], $videoPath) || 
        !move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailPath)) {
        return false;
    }
    
    // Get video duration using FFmpeg or fallback to a default value
    $duration = 0;
    if (function_exists('shell_exec') && is_executable('c:/ffmpeg/bin/ffmpeg.exe')) {
        $command = 'c:/ffmpeg/bin/ffmpeg.exe -i "' . $videoPath . '" 2>&1';
        $output = shell_exec($command);
        
        // Parse duration from FFmpeg output
        if (preg_match('/Duration: ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $output, $matches)) {
            $hours = (int)$matches[1];
            $minutes = (int)$matches[2];
            $seconds = (int)$matches[3];
            
            $duration = $hours * 3600 + $minutes * 60 + $seconds;
        }
    } else {
        // Fallback: Try to get file size and estimate duration (very rough estimate)
        $fileSize = filesize($videoPath);
        // Rough estimate: 1MB ≈ 10 seconds of video at moderate quality
        $duration = round($fileSize / (1024 * 1024) * 10);
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO videos (title, description, filename, thumbnail, duration, category, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $title, $description, $videoFilename, $thumbnailFilename, $duration, $category
        ]);
        
        return true;
    } catch (PDOException $e) {
        // Delete uploaded files if database insert fails
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }
        
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
        
        return false;
    }
}

/**
 * Update an existing video
 * 
 * @param int $videoId Video ID
 * @param string $title Video title
 * @param string $description Video description
 * @param string $category Video category
 * @param array $videoFile Uploaded video file ($_FILES['video_file'])
 * @param array $thumbnailFile Uploaded thumbnail file ($_FILES['thumbnail'])
 * @return bool True if successful, false otherwise
 */
function updateVideo($videoId, $title, $description, $category, $videoFile, $thumbnailFile) {
    global $pdo;
    
    // Validate inputs
    if (empty($videoId) || empty($title) || empty($description) || empty($category)) {
        return false;
    }
    
    // Get existing video
    $existingVideo = getVideoById($videoId);
    if (!$existingVideo) {
        return false;
    }
    
    $videoFilename = $existingVideo['filename'];
    $thumbnailFilename = $existingVideo['thumbnail'];
    
    // Handle video file upload if provided
    if ($videoFile['error'] === UPLOAD_ERR_OK) {
        // Generate unique filename
        $videoFilename = uniqid() . '_' . basename($videoFile['name']);
        
        // Define upload path
        $videoPath = '../assets/videos/' . $videoFilename;
        
        // Create directory if it doesn't exist
        if (!file_exists('../assets/videos/')) {
            mkdir('../assets/videos/', 0777, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($videoFile['tmp_name'], $videoPath)) {
            return false;
        }
        
        // Delete old video file
        $oldVideoPath = '../assets/videos/' . $existingVideo['filename'];
        if (file_exists($oldVideoPath)) {
            unlink($oldVideoPath);
        }
    }
    
    // Handle thumbnail file upload if provided
    if ($thumbnailFile['error'] === UPLOAD_ERR_OK) {
        // Generate unique filename
        $thumbnailFilename = uniqid() . '_' . basename($thumbnailFile['name']);
        
        // Define upload path
        $thumbnailPath = '../assets/images/thumbnails/' . $thumbnailFilename;
        
        // Create directory if it doesn't exist
        if (!file_exists('../assets/images/thumbnails/')) {
            mkdir('../assets/images/thumbnails/', 0777, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailPath)) {
            return false;
        }
        
        // Delete old thumbnail file
        $oldThumbnailPath = '../assets/images/thumbnails/' . $existingVideo['thumbnail'];
        if (file_exists($oldThumbnailPath)) {
            unlink($oldThumbnailPath);
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE videos 
            SET title = ?, description = ?, filename = ?, thumbnail = ?, category = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $title, $description, $videoFilename, $thumbnailFilename, $category, $videoId
        ]);
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Delete a video
 * 
 * @param int $videoId Video ID
 * @return bool True if successful, false otherwise
 */
function deleteVideo($videoId) {
    global $pdo;
    
    // Get existing video
    $existingVideo = getVideoById($videoId);
    if (!$existingVideo) {
        return false;
    }
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete from watched_videos table first (foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM watched_videos WHERE video_id = ?");
        $stmt->execute([$videoId]);
        
        // Delete from videos table
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->execute([$videoId]);
        
        // Commit transaction
        $pdo->commit();
        
        // Delete files
        $videoPath = '../assets/videos/' . $existingVideo['filename'];
        $thumbnailPath = '../assets/images/thumbnails/' . $existingVideo['thumbnail'];
        
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }
        
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
        
        return true;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        return false;
    }
}



/**
 * Get categories with pagination and search
 * 
 * @param int $limit Number of categories per page
 * @param int $offset Pagination offset
 * @param string $search Search term
 * @return array Array of categories
 */
function getCategories($limit = 10, $offset = 0, $search = '') {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM categories WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY name ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get total number of categories with search
 * 
 * @param string $search Search term
 * @return int Total number of categories
 */
function getTotalCategoriesCount($search = '') {
    global $pdo;
    
    try {
        $sql = "SELECT COUNT(*) as total FROM categories WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetch();
        
        return $data['total'];
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Get category by ID
 * 
 * @param int $categoryId Category ID
 * @return array|null Category data or null if not found
 */
function getCategoryById($categoryId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Add a new category
 * 
 * @param string $name Category name
 * @param string $description Category description
 * @return bool True if successful, false otherwise
 */
function addCategory($name, $description) {
    global $pdo;
    
    // Validate inputs
    if (empty($name)) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        return true;
    } catch (PDOException $e) {
        // Category name might already exist (unique constraint)
        return false;
    }
}

/**
 * Update an existing category
 * 
 * @param int $categoryId Category ID
 * @param string $name Category name
 * @param string $description Category description
 * @return bool True if successful, false otherwise
 */
function updateCategory($categoryId, $name, $description) {
    global $pdo;
    
    // Validate inputs
    if (empty($categoryId) || empty($name)) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $categoryId]);
        return true;
    } catch (PDOException $e) {
        // Category name might already exist (unique constraint)
        return false;
    }
}

/**
 * Delete a category
 * 
 * @param int $categoryId Category ID
 * @return bool True if successful, false otherwise
 */
function deleteCategory($categoryId) {
    global $pdo;
    
    try {
        // Check if category is used by any videos
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM videos WHERE category = (SELECT name FROM categories WHERE id = ?)");
        $stmt->execute([$categoryId]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            // Category is in use, don't delete
            return false;
        }
        
        // Delete the category
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Get the number of videos in a category
 * 
 * @param string $categoryName Category name
 * @return int Number of videos in the category
 */
function getCategoryVideoCount($categoryName) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM videos WHERE category = ?");
        $stmt->execute([$categoryName]);
        $result = $stmt->fetch();
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}



/**
 * Calculate video price based on file size
 * 
 * @param string $videoPath Path to the video file
 * @return float Price in RWF
 */
function calculateVideoPrice($videoPath) {
    // Check if file exists
    if (!file_exists($videoPath)) {
        return 0;
    }
    
    // Get file size in MB
    $fileSizeMB = filesize($videoPath) / (1024 * 1024);
    
    // Base price: 500 RWF per MB with a minimum of 1000 RWF
    $price = max(1000, round($fileSizeMB * 500, 0));
    
    // Round to nearest 100 RWF
    $price = ceil($price / 100) * 100;
    
    return $price;
}

/**
 * Format price in RWF
 * 
 * @param float $price Price to format
 * @return string Formatted price
 */
function formatPrice($price) {
    return number_format($price, 0, '.', ',') . ' RWF';
}

?>