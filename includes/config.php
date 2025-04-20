<?php
// Start session
session_start();

// Site configuration
define('SITE_NAME', 'VideoStream');
define('SITE_URL', 'http://localhost/videos');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/videos/assets/videos/');
define('THUMBNAIL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/videos/assets/images/thumbnails/');
define('PROFILE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/videos/assets/images/profiles/');

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('UTC');
?>