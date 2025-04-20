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

$pageTitle = 'Manage Users';

// Handle user actions
$message = '';
$messageType = '';

// Delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $userId = $_GET['delete'];
    
    // Don't allow deleting yourself
    if ($userId == $_SESSION['user_id']) {
        $message = 'You cannot delete your own account.';
        $messageType = 'error';
    } else {
        if (deleteUser($userId)) {
            $message = 'User deleted successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete user.';
            $messageType = 'error';
        }
    }
}

// Toggle admin status
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $userId = $_GET['toggle_admin'];
    
    // Don't allow changing your own admin status
    if ($userId == $_SESSION['user_id']) {
        $message = 'You cannot change your own admin status.';
        $messageType = 'error';
    } else {
        if (toggleAdminStatus($userId)) {
            $message = 'User admin status updated successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to update user admin status.';
            $messageType = 'error';
        }
    }
}

// Get user for viewing
$viewUser = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $viewUser = getUserById($_GET['view']);
    if ($viewUser) {
        $userVideos = getUserWatchedVideos($viewUser['id']);
    }
}

// Get all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';

$users = getUsers($limit, $offset, $search, $roleFilter);
$totalUsers = getTotalUsersCount($search, $roleFilter);
$totalPages = ceil($totalUsers / $limit);
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
                    <li class="active">
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
                        <form action="<?= SITE_URL ?>/admin/users.php" method="get">
                            <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
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
            
            <!-- Users Content -->
            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= $message ?>
                    </div>
                <?php endif; ?>
                
                <div class="admin-actions">
                    <div class="filter-controls">
                        <form action="<?= SITE_URL ?>/admin/users.php" method="get" class="filter-form">
                            <select name="role" class="filter-select">
                                <option value="">All Users</option>
                                <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admins Only</option>
                                <option value="regular" <?= $roleFilter === 'regular' ? 'selected' : '' ?>>Regular Users</option>
                            </select>
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- User View Modal -->
                <?php if ($viewUser): ?>
                <div id="userViewModal" class="modal active">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>User Profile: <?= $viewUser['username'] ?></h2>
                            <a href="<?= SITE_URL ?>/admin/users.php" class="modal-close">&times;</a>
                        </div>
                        <div class="modal-body">
                            <div class="user-profile-view">
                                <div class="user-profile-header">
                                    <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $viewUser['profile_image'] ?>" alt="<?= $viewUser['username'] ?>" class="profile-image">
                                    
                                    <div class="user-profile-info">
                                        <h3><?= $viewUser['username'] ?></h3>
                                        <p class="user-email"><?= $viewUser['email'] ?></p>
                                        
                                        <div class="user-badges">
                                            <span class="badge <?= $viewUser['is_admin'] ? 'badge-admin' : 'badge-user' ?>">
                                                <i class="fas fa-<?= $viewUser['is_admin'] ? 'crown' : 'user' ?>"></i>
                                                <?= $viewUser['is_admin'] ? 'Administrator' : 'Regular User' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="user-profile-details">
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-calendar-alt"></i> Joined
                                        </div>
                                        <div class="detail-value">
                                            <?= date('F j, Y', strtotime($viewUser['created_at'])) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-clock"></i> Last Login
                                        </div>
                                        <div class="detail-value">
                                            <?= $viewUser['last_login'] ? date('F j, Y g:i a', strtotime($viewUser['last_login'])) : 'Never' ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-film"></i> Videos Watched
                                        </div>
                                        <div class="detail-value">
                                            <?= count($userVideos) ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="fas fa-check-circle"></i> Completed Videos
                                        </div>
                                        <div class="detail-value">
                                            <?= count(array_filter($userVideos, function($v) { return $v['completed'] == 1; })) ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="user-actions">
                                    <a href="<?= SITE_URL ?>/admin/users.php?toggle_admin=<?= $viewUser['id'] ?>" class="btn <?= $viewUser['is_admin'] ? 'btn-warning' : 'btn-success' ?>">
                                        <i class="fas fa-<?= $viewUser['is_admin'] ? 'user' : 'crown' ?>"></i>
                                        <?= $viewUser['is_admin'] ? 'Remove Admin' : 'Make Admin' ?>
                                    </a>
                                    
                                    <?php if ($viewUser['id'] != $_SESSION['user_id']): ?>
                                    <a href="<?= SITE_URL ?>/admin/users.php?delete=<?= $viewUser['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete User
                                    </a>
                                    <?php endif; ?>
                                </div>
                                
                                <h3 class="section-title">Watch History</h3>
                                
                                <?php if (empty($userVideos)): ?>
                                    <div class="no-results">
                                        <p>This user hasn't watched any videos yet.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="admin-table">
                                            <thead>
                                                <tr>
                                                    <th>Video</th>
                                                    <th>Progress</th>
                                                    <th>Last Watched</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($userVideos as $video): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="video-info-cell">
                                                                <img src="<?= SITE_URL ?>/assets/images/thumbnails/<?= $video['thumbnail'] ?>" alt="<?= $video['title'] ?>">
                                                                <div>
                                                                    <a href="<?= SITE_URL ?>/video.php?id=<?= $video['id'] ?>"><?= $video['title'] ?></a>
                                                                    <span class="video-category"><?= $video['category'] ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="progress-bar">
                                                                <div class="progress" style="width: <?= $video['watch_percentage'] ?>%"></div>
                                                            </div>
                                                            <span class="progress-text"><?= $video['watch_percentage'] ?>%</span>
                                                        </td>
                                                        <td><?= date('M d, Y g:i a', strtotime($video['watched_at'])) ?></td>
                                                        <td>
                                                            <span class="status-badge <?= $video['completed'] ? 'completed' : 'in-progress' ?>">
                                                                <i class="fas fa-<?= $video['completed'] ? 'check-circle' : 'clock' ?>"></i>
                                                                <?= $video['completed'] ? 'Completed' : 'In Progress' ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Users Table -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-users"></i> All Users</h2>
                        <div class="header-actions">
                            <span class="item-count"><?= $totalUsers ?> users</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No users found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $user['id'] ?></td>
                                                <td>
                                                    <div class="user-info">
                                                        <img src="<?= SITE_URL ?>/assets/images/profiles/<?= $user['profile_image'] ?>" alt="<?= $user['username'] ?>">
                                                        <span><?= $user['username'] ?></span>
                                                    </div>
                                                </td>
                                                <td><?= $user['email'] ?></td>
                                                <td>
                                                    <span class="role-badge <?= $user['is_admin'] ? 'admin' : 'user' ?>">
                                                        <?= $user['is_admin'] ? 'Admin' : 'User' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                                <td><?= $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never' ?></td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a href="<?= SITE_URL ?>/admin/users.php?view=<?= $user['id'] ?>" class="action-btn view" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= SITE_URL ?>/admin/users.php?toggle_admin=<?= $user['id'] ?>" class="action-btn <?= $user['is_admin'] ? 'warning' : 'success' ?>" title="<?= $user['is_admin'] ? 'Remove Admin' : 'Make Admin' ?>">
                                                            <i class="fas fa-<?= $user['is_admin'] ? 'user' : 'crown' ?>"></i>
                                                        </a>
                                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                            <a href="<?= SITE_URL ?>/admin/users.php?delete=<?= $user['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
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
                                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>" class="pagination-item">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>" class="pagination-item <?= $i === $page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($roleFilter) ?>" class="pagination-item">
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