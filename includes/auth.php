<?php
require_once 'config.php';
require_once 'db.php';

// Register a new user
function registerUser($username, $email, $password) {
    global $pdo;
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}

// Login user
function loginUser($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Create session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();
            
            // Create session record in database
            createSession($user['id']);
            
            return true;
        }
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

// Create session record
function createSession($userId) {
    global $pdo;
    
    $sessionId = session_id();
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $expiresAt = date('Y-m-d H:i:s', time() + SESSION_TIMEOUT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO sessions (id, user_id, ip_address, user_agent, expires_at) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$sessionId, $userId, $ipAddress, $userAgent, $expiresAt]);
    } catch (PDOException $e) {
        // Session already exists, update it
        $stmt = $pdo->prepare("UPDATE sessions SET expires_at = ? WHERE id = ?");
        $stmt->execute([$expiresAt, $sessionId]);
    }
}

// Check if user is logged in
function isLoggedIn() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // Check session timeout
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        return true;
    }
    return false;
}

// Logout user
function logout() {
    
    // Destroy session
    session_unset();
    session_destroy();
}

// Get current user data
function getCurrentUser() {
    global $pdo;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, profile_image, created_at, last_login 
                              FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Check if a user is an admin
 * 
 * @return bool True if user is admin, false otherwise
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Authenticate admin user
 * 
 * @param string $username Username
 * @param string $password Password
 * @return bool True if authentication successful, false otherwise
 */
function authenticateAdmin($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_admin = 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login time
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = true;
            
            // Get admin role
            $roleStmt = $pdo->prepare("SELECT role FROM admins WHERE user_id = ?");
            $roleStmt->execute([$user['id']]);
            $adminData = $roleStmt->fetch();
            
            if ($adminData) {
                $_SESSION['admin_role'] = $adminData['role'];
            } else {
                $_SESSION['admin_role'] = 'editor'; // Default role
            }
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        return false;
    }
}
?>