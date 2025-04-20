<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Get query parameters
$category = isset($_GET['category']) ? $_GET['category'] : null;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 12;
$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

// Get videos
$videos = [];
if ($category) {
    $videos = getVideosByCategory($category);
} else {
    $videos = getAllVideos();
}

// Apply limit and offset
$videos = array_slice($videos, $offset, $limit);

// Format response
$response = [
    'success' => true,
    'count' => count($videos),
    'videos' => $videos
];

header('Content-Type: application/json');
echo json_encode($response);
?>