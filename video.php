<?php
$pageTitle = 'Watch Video';
$extraCSS = ['video.css'];
$extraJS = ['video.js'];

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Check if video ID is provided
if (!isset($_GET['id'])) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

$videoId = (int) $_GET['id'];
$video = getVideoById($videoId);

// If video doesn't exist, redirect to dashboard
if (!$video) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

// Get watch status if user is logged in
$watchStatus = null;
if (isLoggedIn()) {
    $watchStatus = getWatchStatus($_SESSION['user_id'], $videoId);
}

// Update page title
$pageTitle = $video['title'] . ' - ' . SITE_NAME;

require_once 'includes/header.php';
?>

<div class="container">
    <div class="video-page">
        <div class="video-player-container">
            <video id="videoPlayer" class="video-player" poster="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" preload="metadata">
                <source src="<?= SITE_URL ?>/assets/videos/<?= $video['filename'] ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            
            <div class="video-controls">
                <button id="playPauseBtn" class="control-button">
                    <i class="fas fa-play"></i>
                </button>
                
                <div class="time-display">
                    <span id="currentTime">0:00</span> / <span id="duration">0:00</span>
                </div>
                
                <div class="volume-container">
                    <button id="muteBtn" class="control-button">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    <input type="range" id="volumeSlider" class="volume-slider" min="0" max="1" step="0.1" value="1">
                </div>
                
                <button id="fullscreenBtn" class="control-button">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
            
            <div class="progress-container">
                <div id="progressBar" class="progress-bar"></div>
            </div>
            
            <div class="video-player-overlay">
                <div id="bigPlayButton" class="big-play-button">
                    <i class="fas fa-play"></i>
                </div>
            </div>
            
            <div id="completionMessage" class="completion-message">
                <div class="completion-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Video Completed!</h3>
                <p>Great job! You've completed this video.</p>
                <button id="replayBtn" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Watch Again
                </button>
            </div>
        </div>
        
        <div class="video-details">
            <h1><?= $video['title'] ?></h1>
            
            <div class="video-meta">
                <div class="video-category">
                    <i class="fas fa-folder"></i> <?= $video['category'] ?>
                </div>
                <div class="video-duration">
                    <i class="fas fa-clock"></i> <?= formatDuration($video['duration']) ?>
                </div>
                <div class="video-date">
                    <i class="fas fa-calendar-alt"></i> <?= date('M d, Y', strtotime($video['created_at'])) ?>
                </div>
                <div class="video-price">
                    <i class="fas fa-tag"></i> <?= formatPrice($video['price']) ?>
                </div>
                
                <?php if ($watchStatus): ?>
                    <div class="video-progress tooltip" data-tooltip="<?= $watchStatus['watch_percentage'] ?>% watched">
                        <i class="fas <?= $watchStatus['completed'] ? 'fa-check-circle' : 'fa-spinner' ?>"></i>
                        <?= $watchStatus['completed'] ? 'Completed' : 'In Progress' ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="video-description">
                <h3>Description</h3>
                <p><?= nl2br($video['description']) ?></p>
            </div>
        </div>
        
        <?php if (!isLoggedIn()): ?>
            <div class="login-prompt">
                <div class="login-prompt-content">
                    <i class="fas fa-user-lock"></i>
                    <h3>Track Your Progress</h3>
                    <p>Sign in to track your progress and continue where you left off.</p>
                    <div class="login-prompt-buttons">
                        <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-secondary">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="related-videos">
            <h2 class="section-title"><i class="fas fa-film"></i> Related Videos</h2>
            
            <div class="video-grid">
                <?php 
                $relatedVideos = getVideosByCategory($video['category']);
                $relatedVideos = array_filter($relatedVideos, function($v) use ($videoId) {
                    return $v['id'] != $videoId;
                });
                $relatedVideos = array_slice($relatedVideos, 0, 3);
                
                foreach ($relatedVideos as $relatedVideo): 
                ?>
                    <div class="video-card animated fadeIn">
                        <div class="video-thumbnail">
                            <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $relatedVideo['thumbnail'] ?>" alt="<?= $relatedVideo['title'] ?>">
                            <span class="video-duration"><?= formatDuration($relatedVideo['duration']) ?></span>
                            <a href="<?= SITE_URL ?>/video.php?id=<?= $relatedVideo['id'] ?>" class="play-button">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?= $relatedVideo['title'] ?></h3>
                            <p class="video-category"><?= $relatedVideo['category'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Pass video data to JavaScript
    const videoData = {
        id: <?= $videoId ?>,
        duration: <?= $video['duration'] ?>,
        lastPosition: <?= $watchStatus ? $watchStatus['last_position'] : 0 ?>,
        isLoggedIn: <?= isLoggedIn() ? 'true' : 'false' ?>,
        apiUrl: '<?= SITE_URL ?>/api/update_watch_status.php'
    };
</script>

<?php require_once 'includes/footer.php'; ?>