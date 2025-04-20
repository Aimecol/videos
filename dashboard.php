<?php
$pageTitle = 'Dashboard';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

// Get videos based on filters
$videos = [];
if ($search) {
    // Search functionality would be implemented here
    // For now, just get all videos
    $videos = getAllVideos();
} elseif ($category) {
    $videos = getVideosByCategory($category);
} else {
    $videos = getAllVideos();
}

// Get all categories for filter dropdown
$categories = getAllCategories();

require_once 'includes/header.php';
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <?php if ($category): ?>
                    <i class="fas fa-folder"></i> <?= htmlspecialchars($category) ?> Videos
                <?php elseif ($search): ?>
                    <i class="fas fa-search"></i> Search Results for "<?= htmlspecialchars($search) ?>"
                <?php else: ?>
                    <i class="fas fa-compass"></i> Explore Videos
                <?php endif; ?>
            </h1>
            
            <div class="filter-container">
                <form action="<?= SITE_URL ?>/dashboard.php" method="get" class="search-form">
                    <input type="text" name="search" placeholder="Search videos..." class="search-input" value="<?= htmlspecialchars($search ?? '') ?>">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <select name="category" class="filter-select" onchange="location = this.value;">
                    <option value="<?= SITE_URL ?>/dashboard.php">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= SITE_URL ?>/dashboard.php?category=<?= urlencode($cat['name']) ?>" <?= $category === $cat['name'] ? 'selected' : '' ?>>
                            <?= $cat['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <?php if (empty($videos)): ?>
            <div class="no-results">
                <i class="fas fa-film"></i>
                <h2>No Videos Found</h2>
                <p>We couldn't find any videos matching your criteria.</p>
                <a href="<?= SITE_URL ?>/dashboard.php" class="btn btn-primary">
                    <i class="fas fa-redo"></i> View All Videos
                </a>
            </div>
        <?php else: ?>
            <div class="video-grid">
                <?php foreach ($videos as $video): ?>
                    <div class="video-card animated fadeIn">
                        <div class="video-thumbnail">
                            <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                            <span class="video-duration"><?= formatDuration($video['duration']) ?></span>
                            
                            <?php if (isLoggedIn()): ?>
                                <?php $status = getWatchStatus($_SESSION['user_id'], $video['id']); ?>
                                <?php if ($status): ?>
                                    <div class="watch-progress-indicator" style="width: <?= $status['watch_percentage'] ?>%"></div>
                                    <?php if ($status['completed']): ?>
                                        <div class="watch-completed-badge">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <a href="<?= SITE_URL ?>/video.php?id=<?= $video['id'] ?>" class="play-button">
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?= $video['title'] ?></h3>
                            <p class="video-category"><?= $video['category'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>