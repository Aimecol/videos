<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Redirect if already logged in as admin
if (isLoggedIn() && isAdmin()) {
    header('Location: ' . SITE_URL . '/admin/index.php');
    exit;
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if (authenticateAdmin($username, $password)) {
            // Redirect to admin dashboard
            header('Location: ' . SITE_URL . '/admin/index.php');
            exit;
        } else {
            $error = 'Invalid credentials or you do not have admin privileges.';
        }
    }
}

$pageTitle = 'Admin Login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/css/admin.css">
    <style>
        .admin-login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--background-alt);
        }
        
        .admin-login-card {
            width: 400px;
            background-color: var(--background-color);
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .login-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .login-header h1 {
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-header i {
            margin-right: 10px;
            font-size: 1.8rem;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .login-form .form-group {
            margin-bottom: 20px;
        }
        
        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .login-form .input-group {
            position: relative;
        }
        
        .login-form .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        
        .login-form input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .login-form input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        .login-form button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .login-form button:hover {
            background-color: var(--secondary-color);
        }
        
        .login-footer {
            text-align: center;
            padding: 0 30px 30px;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="login-header">
                <h1><i class="fas fa-lock"></i> Admin Login</h1>
            </div>
            
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" method="post" action="">
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" id="username" name="username" placeholder="Enter your username or email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <i class="fas fa-key"></i>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <button type="submit">Login</button>
                </form>
            </div>
            
            <div class="login-footer">
                <a href="<?= SITE_URL ?>">Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>