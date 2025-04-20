<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['videoId']) || !isset($data['percentage']) || !isset($data['position'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$videoId = (int) $data['videoId'];
$percentage = (int) $data['percentage'];
$position = (int) $data['position'];
$completed = isset($data['completed']) ? (bool) $data['completed'] : false;

// If percentage is 100 or greater, mark as completed
if ($percentage >= 100) {
    $completed = true;
}

// Update watch status
$success = updateWatchStatus($_SESSION['user_id'], $videoId, $percentage, $position, $completed);

header('Content-Type: application/json');
echo json_encode([
    'success' => $success,
    'message' => $success ? 'Watch status updated' : 'Failed to update watch status',
    'data' => [
        'videoId' => $videoId,
        'percentage' => $percentage,
        'position' => $position,
        'completed' => $completed
    ]
]);
?>