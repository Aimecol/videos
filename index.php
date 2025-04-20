<?php
$pageTitle = 'Home';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Get featured videos
$featuredVideos = getAllVideos();
$featuredVideos = array_slice($featuredVideos, 0, 5);

// Get categories
$categories = getAllCategories();

require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="animated fadeInDown">Discover Amazing Videos</h1>
            <p class="animated fadeInUp">Watch, learn, and track your progress with our video platform.</p>
            
            <?php if (!$currentUser): ?>
                <div class="hero-buttons animated fadeInUp">
                    <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Get Started
                    </a>
                    <a href="<?= SITE_URL ?>/auth/login.php" class="btn btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
            <?php else: ?>
                <div class="hero-buttons animated fadeInUp">
                    <a href="<?= SITE_URL ?>/dashboard.php" class="btn btn-primary">
                        <i class="fas fa-compass"></i> Explore Videos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="featured-videos">
    <div class="container">
        <h2 class="section-title"><i class="fas fa-star"></i> Featured Videos</h2>
        
        <div class="video-slider">
            <?php foreach ($featuredVideos as $video): ?>
                <div class="video-card animated fadeIn">
                    <div class="video-thumbnail">
                        <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                        <span class="video-duration"><?= formatDuration($video['duration']) ?></span>
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
    </div>
</section>

<section class="categories">
    <div class="container">
        <h2 class="section-title"><i class="fas fa-list"></i> Browse Categories</h2>
        
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="<?= SITE_URL ?>/dashboard.php?category=<?= urlencode($category['name']) ?>" class="category-card animated fadeIn">
                    <div class="category-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <h3 class="category-name"><?= $category['name'] ?></h3>
                    <p class="category-description"><?= substr($category['description'], 0, 60) ?>...</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title"><i class="fas fa-rocket"></i> Platform Features</h2>
        
        <div class="features-grid">
            <div class="feature-card animated fadeInUp">
                <div class="feature-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3>Secure Authentication</h3>
                <p>Create your account and access your personalized dashboard securely.</p>
            </div>
            
            <div class="feature-card animated fadeInUp" data-delay="0.2s">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <h3>Progress Tracking</h3>
                <p>We automatically track your watched videos and remember where you left off.</p>
            </div>
            
            <div class="feature-card animated fadeInUp" data-delay="0.4s">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Responsive Design</h3>
                <p>Enjoy our platform on any device with our fully responsive interface.</p>
            </div>
            
            <div class="feature-card animated fadeInUp" data-delay="0.6s">
                <div class="feature-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>Completion Certificates</h3>
                <p>Earn certificates when you complete video courses in our platform.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div class="cta-content animated fadeIn">
            <h2>Ready to Start Watching?</h2>
            <p>Join thousands of users who are already enjoying our video platform.</p>
            
            <?php if (!$currentUser): ?>
                <div class="cta-buttons">
                    <a href="<?= SITE_URL ?>/auth/register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                </div>
            <?php else: ?>
                <div class="cta-buttons">
                    <a href="<?= SITE_URL ?>/dashboard.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-compass"></i> Browse Videos
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>