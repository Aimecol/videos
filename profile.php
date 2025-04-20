<?php
$pageTitle = 'My Profile';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit;
}

// Get current user data
$user = getCurrentUser();

// Get user's watched videos
$watchedVideos = getUserWatchedVideos($user['id']);

// Count completed videos
$completedVideos = array_filter($watchedVideos, function($video) {
    return $video['completed'] == 1;
});

// Calculate total earnings from completed videos
$totalEarnings = 0;
foreach ($completedVideos as $video) {
    $totalEarnings += $video['price'];
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="profile-page">
        <div class="profile-header">
            <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $user['profile_image'] ?>" alt="<?= $user['username'] ?>" class="profile-image">
            
            <div class="profile-info">
                <h1><?= $user['username'] ?></h1>
                <p><?= $user['email'] ?></p>
                
                <div class="profile-stats">
                    <div class="stat">
                        <div class="stat-value"><?= count($watchedVideos) ?></div>
                        <div class="stat-label">Videos Watched</div>
                    </div>
                    
                    <div class="stat">
                        <div class="stat-value"><?= count($completedVideos) ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                    
                    <div class="stat">
                        <div class="stat-value"><?= formatPrice($totalEarnings) ?></div>
                        <div class="stat-label">Total Earned</div>
                    </div>
                    
                    <div class="stat">
                        <div class="stat-value"><?= date('M Y', strtotime($user['created_at'])) ?></div>
                        <div class="stat-label">Member Since</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="profile-tabs">
            <div class="profile-tab active" data-tab="history">
                <i class="fas fa-history"></i> Watch History
            </div>
            <div class="profile-tab" data-tab="completed">
                <i class="fas fa-check-circle"></i> Completed Videos
            </div>
            <div class="profile-tab" data-tab="settings">
                <i class="fas fa-cog"></i> Account Settings
            </div>
        </div>
        
        <div id="history" class="tab-content active">
            <?php if (empty($watchedVideos)): ?>
                <div class="no-results">
                    <i class="fas fa-history"></i>
                    <h2>No Watch History</h2>
                    <p>You haven't watched any videos yet.</p>
                    <a href="<?= SITE_URL ?>/dashboard.php" class="btn btn-primary">
                        <i class="fas fa-play"></i> Start Watching
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($watchedVideos as $video): ?>
                    <div class="history-item">
                        <div class="history-thumbnail">
                            <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                        </div>
                        
                        <div class="history-info">
                            <h3 class="history-title">
                                <a href="<?= SITE_URL ?>/video.php?id=<?= $video['id'] ?>"><?= $video['title'] ?></a>
                            </h3>
                            
                            <div class="history-meta">
                                <div class="history-category">
                                    <i class="fas fa-folder"></i> <?= $video['category'] ?>
                                </div>
                                <div class="history-duration">
                                    <i class="fas fa-clock"></i> <?= formatDuration($video['duration']) ?>
                                </div>
                                <div class="history-date">
                                    <i class="fas fa-calendar-alt"></i> <?= date('M d, Y', strtotime($video['watched_at'])) ?>
                                </div>
                            </div>
                            
                            <div class="history-progress">
                                <div class="history-progress-bar <?= $video['completed'] ? 'history-completed' : '' ?>" style="width: <?= $video['watch_percentage'] ?>%"></div>
                            </div>
                            
                            <div class="history-status">
                                <?php if ($video['completed']): ?>
                                    <span class="status-badge completed">
                                        <i class="fas fa-check-circle"></i> Completed
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge in-progress">
                                        <i class="fas fa-spinner"></i> <?= $video['watch_percentage'] ?>% watched
                                    </span>
                                <?php endif; ?>
                                
                                <a href="<?= SITE_URL ?>/video.php?id=<?= $video['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-play"></i> <?= $video['completed'] ? 'Watch Again' : 'Continue' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div id="completed" class="tab-content">
            <?php if (empty($completedVideos)): ?>
                <div class="no-results">
                    <i class="fas fa-check-circle"></i>
                    <h2>No Completed Videos</h2>
                    <p>You haven't completed any videos yet.</p>
                    <a href="<?= SITE_URL ?>/dashboard.php" class="btn btn-primary">
                        <i class="fas fa-play"></i> Start Watching
                    </a>
                </div>
            <?php else: ?>
                <div class="completed-summary">
                    <div class="earnings-card">
                        <div class="earnings-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="earnings-details">
                            <h3>Total Earnings</h3>
                            <div class="earnings-amount"><?= formatPrice($totalEarnings) ?></div>
                            <p>From <?= count($completedVideos) ?> completed videos</p>
                        </div>
                    </div>
                </div>
                
                <?php foreach ($completedVideos as $video): ?>
                    <div class="history-item">
                        <div class="history-thumbnail">
                            <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                        </div>
                        
                        <div class="history-info">
                            <h3 class="history-title">
                                <a href="<?= SITE_URL ?>/video.php?id=<?= $video['id'] ?>"><?= $video['title'] ?></a>
                            </h3>
                            
                            <div class="history-meta">
                                <div class="history-category">
                                    <i class="fas fa-folder"></i> <?= $video['category'] ?>
                                </div>
                                <div class="history-duration">
                                    <i class="fas fa-clock"></i> <?= formatDuration($video['duration']) ?>
                                </div>
                                <div class="history-date">
                                    <i class="fas fa-calendar-alt"></i> <?= date('M d, Y', strtotime($video['watched_at'])) ?>
                                </div>
                                <div class="history-price">
                                    <i class="fas fa-tag"></i> <?= formatPrice($video['price']) ?>
                                </div>
                            </div>
                            
                            <div class="history-status">
                                <span class="status-badge completed">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                                <span class="status-badge earned">
                                    <i class="fas fa-coins"></i> Earned: <?= formatPrice($video['price']) ?>
                                </span>
                                
                                <a href="<?= SITE_URL ?>/video.php?id=<?= $video['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-play"></i> Watch Again
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div id="settings" class="tab-content">
            <div class="edit-profile-form">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                
                <form action="<?= SITE_URL ?>/profile_update.php" method="post" enctype="multipart/form-data">
                    <div class="profile-image-upload">
                        <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $user['profile_image'] ?>" alt="<?= $user['username'] ?>" class="current-image">
                        
                        <div class="image-upload-container">
                            <label for="profile_image" class="custom-file-upload">
                                <i class="fas fa-upload"></i> Change Profile Picture
                            </label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?= $user['username'] ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="current_password">Current Password (required to save changes)</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <h3>Change Password (optional)</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .earnings-card {
        background: linear-gradient(135deg, #4361ee, #3f37c9);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .earnings-icon {
        font-size: 2.5rem;
        margin-right: 20px;
        background: rgba(255, 255, 255, 0.2);
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .earnings-details h3 {
        margin: 0 0 5px 0;
        font-size: 1.2rem;
        font-weight: 500;
    }
    
    .earnings-amount {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .earnings-details p {
        margin: 0;
        opacity: 0.8;
    }
    
    .status-badge.earned {
        background-color: #4caf50;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        margin-right: 10px;
    }
    
    .status-badge.earned i {
        margin-right: 5px;
    }
    
    .completed-summary {
        margin-bottom: 20px;
    }
</style>

<script>
    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.profile-tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>