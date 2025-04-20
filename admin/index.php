<?php
// session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
}

// Get dashboard statistics
$totalVideos = getTotalVideos();
$totalUsers = getTotalUsers();
$totalCategories = getTotalCategories();
$recentVideos = getRecentVideos(5);
$recentUsers = getRecentUsers(5);

$pageTitle = 'Admin Dashboard';
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
                    <li class="active">
                        <a href="<?= SITE_URL ?>/admin">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
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
                        <form action="<?= SITE_URL ?>/admin/search.php" method="get">
                            <input type="text" name="query" placeholder="Search...">
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
            
            <!-- Dashboard Content -->
            <div class="admin-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stats-card">
                        <div class="stats-icon videos">
                            <i class="fas fa-film"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?= $totalVideos ?></h3>
                            <p>Total Videos</p>
                        </div>
                        <div class="stats-link">
                            <a href="<?= SITE_URL ?>/admin/videos.php">View All</a>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="stats-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?= $totalUsers ?></h3>
                            <p>Total Users</p>
                        </div>
                        <div class="stats-link">
                            <a href="<?= SITE_URL ?>/admin/users.php">View All</a>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="stats-icon categories">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?= $totalCategories ?></h3>
                            <p>Categories</p>
                        </div>
                        <div class="stats-link">
                            <a href="<?= SITE_URL ?>/admin/categories.php">View All</a>
                        </div>
                    </div>
                    
                    <div class="stats-card">
                        <div class="stats-icon views">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?= getTotalViews() ?></h3>
                            <p>Total Views</p>
                        </div>
                        <div class="stats-link">
                            <a href="<?= SITE_URL ?>/admin/analytics.php">Analytics</a>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="admin-row">
                    <div class="admin-col">
                        <div class="admin-card">
                            <div class="card-header">
                                <h2><i class="fas fa-film"></i> Recent Videos</h2>
                                <a href="<?= SITE_URL ?>/admin/videos.php" class="view-all">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>Thumbnail</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentVideos as $video): ?>
                                                <tr>
                                                    <td>
                                                        <div class="table-thumbnail">
                                                            <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                                                        </div>
                                                    </td>
                                                    <td><?= $video['title'] ?></td>
                                                    <td><?= $video['category'] ?></td>
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-col">
                        <div class="admin-card">
                            <div class="card-header">
                                <h2><i class="fas fa-users"></i> Recent Users</h2>
                                <a href="<?= SITE_URL ?>/admin/users.php" class="view-all">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="admin-table">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Joined</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentUsers as $user): ?>
                                                <tr>
                                                    <td>
                                                        <div class="user-info">
                                                            <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $user['profile_image'] ?>" alt="<?= $user['username'] ?>">
                                                            <span><?= $user['username'] ?></span>
                                                        </div>
                                                    </td>
                                                    <td><?= $user['email'] ?></td>
                                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                                    <td>
                                                        <div class="table-actions">
                                                            <a href="<?= SITE_URL ?>/admin/users.php?view=<?= $user['id'] ?>" class="action-btn view" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="<?= SITE_URL ?>/admin/users.php?edit=<?= $user['id'] ?>" class="action-btn edit" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="<?= SITE_URL ?>/admin/users.php?delete=<?= $user['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?= SITE_URL ?>/admin/js/admin.js"></script>
</body>
</html>