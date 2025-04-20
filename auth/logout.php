<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Logout user
logout();

// Redirect to home page
header('Location: ' . SITE_URL);
exit;
?>