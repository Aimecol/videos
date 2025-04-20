<?php
$pageTitle = 'Login';
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

$error = '';
$email = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        if (loginUser($email, $password)) {
            // Redirect to dashboard
            header('Location: ' . SITE_URL . '/dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-sign-in-alt"></i> Login</h1>
            <p>Welcome back! Please login to your account.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form action="<?= SITE_URL ?>/auth/login.php" method="post" class="auth-form">
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group form-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="<?= SITE_URL ?>/auth/register.php">Register</a></p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>