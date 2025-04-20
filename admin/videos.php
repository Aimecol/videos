<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
}

$pageTitle = 'Manage Videos';

// Handle video actions
$message = '';
$messageType = '';

// Delete video
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $videoId = $_GET['delete'];
    if (deleteVideo($videoId)) {
        $message = 'Video deleted successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to delete video.';
        $messageType = 'error';
    }
}

// Handle form submission for adding/editing video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_video'])) {
    $videoId = isset($_POST['video_id']) ? $_POST['video_id'] : null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    
    // Handle file uploads
    $videoFile = $_FILES['video_file'];
    $thumbnailFile = $_FILES['thumbnail'];
    
    if ($videoId) {
        // Update existing video
        if (updateVideo($videoId, $title, $description, $category, $videoFile, $thumbnailFile)) {
            $message = 'Video updated successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to update video.';
            $messageType = 'error';
        }
    } else {
        // Add new video
        if (addVideo($title, $description, $category, $videoFile, $thumbnailFile)) {
            $message = 'Video added successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to add video.';
            $messageType = 'error';
        }
    }
}

// Get video for editing
$editVideo = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editVideo = getVideoById($_GET['edit']);
}

// Get all categories for dropdown
$categories = getAllCategories();

// Get all videos with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

$videos = getVideos($limit, $offset, $search, $categoryFilter);
$totalVideos = getTotalVideosCount($search, $categoryFilter);
$totalPages = ceil($totalVideos / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <a href="<?= SITE_URL ?>/admin" class="admin-logo">
                    <i class="fas fa-video"></i>
                    <span>Video<span>Admin</span></span>
                </a>
                <button id="sidebarToggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <a href="<?= SITE_URL ?>/admin">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="<?= SITE_URL ?>/admin/videos.php">
                            <i class="fas fa-film"></i>
                            <span>Videos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/categories.php">
                            <i class="fas fa-folder"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/users.php">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/settings.php">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Navigation -->
            <div class="admin-topbar">
                <div class="topbar-left">
                    <h1 class="page-title"><?= $pageTitle ?></h1>
                </div>
                <div class="topbar-right">
                    <div class="admin-search">
                        <form action="<?= SITE_URL ?>/admin/videos.php" method="get">
                            <input type="text" name="search" placeholder="Search videos..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <div class="admin-profile">
                        <div class="profile-dropdown">
                            <div class="dropdown-toggle">
                                <?php $user = getCurrentUser(); ?>
                                <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $user['profile_image'] ?>" alt="<?= $user['username'] ?>">
                                <span><?= $user['username'] ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="dropdown-menu">
                                <div class="dropdown-header">
                                    <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $user['profile_image'] ?>" alt="<?= $user['username'] ?>">
                                    <h4><?= $user['username'] ?></h4>
                                    <p><?= $user['email'] ?></p>
                                </div>
                                <div class="dropdown-body">
                                    <a href="<?= SITE_URL ?>/admin/profile.php">
                                        <i class="fas fa-user"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="<?= SITE_URL ?>/admin/settings.php">
                                        <i class="fas fa-cog"></i>
                                        <span>Settings</span>
                                    </a>
                                    <a href="<?= SITE_URL ?>">
                                        <i class="fas fa-home"></i>
                                        <span>View Site</span>
                                    </a>
                                </div>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-footer">
                                    <a href="<?= SITE_URL ?>/auth/logout.php">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Videos Content -->
            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= $message ?>
                    </div>
                <?php endif; ?>
                
                <div class="admin-actions">
                    <button id="addVideoBtn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Video
                    </button>
                    
                    <div class="filter-controls">
                        <form action="<?= SITE_URL ?>/admin/videos.php" method="get" class="filter-form">
                            <select name="category" class="filter-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['name'] ?>" <?= $categoryFilter === $cat['name'] ? 'selected' : '' ?>>
                                        <?= $cat['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Video Form Modal -->
                <div id="videoFormModal" class="modal <?= $editVideo ? 'active' : '' ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><?= $editVideo ? 'Edit Video' : 'Add New Video' ?></h2>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= SITE_URL ?>/admin/videos.php" method="post" enctype="multipart/form-data">
                                <?php if ($editVideo): ?>
                                    <input type="hidden" name="video_id" value="<?= $editVideo['id'] ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" id="title" name="title" value="<?= $editVideo ? $editVideo['title'] : '' ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" rows="5" required><?= $editVideo ? $editVideo['description'] : '' ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['name'] ?>" <?= $editVideo && $editVideo['category'] === $cat['name'] ? 'selected' : '' ?>>
                                                <?= $cat['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="video_file">Video File <?= $editVideo ? '(Leave empty to keep current)' : '' ?></label>
                                        <input type="file" id="video_file" name="video_file" accept="video/*" <?= $editVideo ? '' : 'required' ?>>
                                        <?php if ($editVideo): ?>
                                            <p class="form-help">Current: <?= $editVideo['filename'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="thumbnail">Thumbnail <?= $editVideo ? '(Leave empty to keep current)' : '' ?></label>
                                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" <?= $editVideo ? '' : 'required' ?>>
                                        <?php if ($editVideo): ?>
                                            <div class="thumbnail-preview">
                                                <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $editVideo['thumbnail'] ?>" alt="Current Thumbnail">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="save_video" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?= $editVideo ? 'Update Video' : 'Add Video' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Videos Table -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-film"></i> All Videos</h2>
                        <div class="header-actions">
                            <span class="item-count"><?= $totalVideos ?> videos</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Thumbnail</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Date Added</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($videos)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No videos found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($videos as $video): ?>
                                            <tr>
                                                <td><?= $video['id'] ?></td>
                                                <td>
                                                    <div class="table-thumbnail">
                                                        <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                                                    </div>
                                                </td>
                                                <td><?= $video['title'] ?></td>
                                                <td><?= $video['category'] ?></td>
                                                <td><?= formatDuration($video['duration']) ?></td>
                                                <td><?= date('M d, Y', strtotime($video['created_at'])) ?></td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a href="<?= SITE_URL ?>/video.php?id=<?= $video['id'] ?>" class="action-btn view" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= SITE_URL ?>/admin/videos.php?edit=<?= $video['id'] ?>" class="action-btn edit" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= SITE_URL ?>/admin/videos.php?delete=<?= $video['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this video?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($categoryFilter) ?>" class="pagination-item">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($categoryFilter) ?>" class="pagination-item <?= $i === $page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($categoryFilter) ?>" class="pagination-item">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= SITE_URL ?>/admin/js/admin.js"></script>
</body>
</html>