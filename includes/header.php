<?php
require_once 'config.php';
require_once 'db.php';
require_once 'auth.php';
require_once 'functions.php';

$currentUser = isLoggedIn() ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?= SITE_URL ?>">
                    <i class="fas fa-play-circle"></i>
                    <span><?= SITE_NAME ?></span>
                </a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="<?= SITE_URL ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?= SITE_URL ?>/dashboard.php"><i class="fas fa-compass"></i> Explore</a></li>
                    <?php if ($currentUser): ?>
                        <li><a href="<?= SITE_URL ?>/profile.php"><i class="fas fa-history"></i> History</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="user-actions">
                <?php if ($currentUser): ?>
                    <div class="user-profile">
                        <div class="profile-toggle">
                            <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $currentUser['profile_image'] ?>" alt="<?= $currentUser['username'] ?>">
                            <span><?= $currentUser['username'] ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="profile-dropdown">
                            <ul>
                                <li><a href="<?= SITE_URL ?>/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                                <li><a href="<?= SITE_URL ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-register"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
            
            <div class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>
    
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <div class="logo">
                <i class="fas fa-play-circle"></i>
                <span><?= SITE_NAME ?></span>
            </div>
            <div class="mobile-menu-close">
                <i class="fas fa-times"></i>
            </div>
        </div>
        
        <nav class="mobile-nav">
            <ul>
                <li><a href="<?= SITE_URL ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?= SITE_URL ?>/dashboard.php"><i class="fas fa-compass"></i> Explore</a></li>
                <?php if ($currentUser): ?>
                    <li><a href="<?= SITE_URL ?>/profile.php"><i class="fas fa-history"></i> History</a></li>
                    <li><a href="<?= SITE_URL ?>/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="<?= SITE_URL ?>/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= SITE_URL ?>/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="<?= SITE_URL ?>/auth/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    
    <main class="main-content">