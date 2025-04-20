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

$pageTitle = 'Manage Categories';

// Handle category actions
$message = '';
$messageType = '';

// Delete category
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $categoryId = $_GET['delete'];
    if (deleteCategory($categoryId)) {
        $message = 'Category deleted successfully.';
        $messageType = 'success';
    } else {
        $message = 'Failed to delete category. It may be in use by videos.';
        $messageType = 'error';
    }
}

// Handle form submission for adding/editing category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $categoryId = isset($_POST['category_id']) ? $_POST['category_id'] : null;
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    if ($categoryId) {
        // Update existing category
        if (updateCategory($categoryId, $name, $description)) {
            $message = 'Category updated successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to update category.';
            $messageType = 'error';
        }
    } else {
        // Add new category
        if (addCategory($name, $description)) {
            $message = 'Category added successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to add category.';
            $messageType = 'error';
        }
    }
}

// Get category for editing
$editCategory = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editCategory = getCategoryById($_GET['edit']);
}

// Get all categories with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';

$categories = getCategories($limit, $offset, $search);
$totalCategories = getTotalCategoriesCount($search);
$totalPages = ceil($totalCategories / $limit);
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
                    <li class="active">
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
                        <form action="<?= SITE_URL ?>/admin/categories.php" method="get">
                            <input type="text" name="search" placeholder="Search categories..." value="<?= htmlspecialchars($search) ?>">
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
            
            <!-- Categories Content -->
            <div class="admin-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?>">
                        <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                        <?= $message ?>
                    </div>
                <?php endif; ?>
                
                <div class="admin-actions">
                    <button id="addCategoryBtn" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Category
                    </button>
                </div>
                
                <!-- Category Form Modal -->
                <div id="categoryFormModal" class="modal <?= $editCategory ? 'active' : '' ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><?= $editCategory ? 'Edit Category' : 'Add New Category' ?></h2>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= SITE_URL ?>/admin/categories.php" method="post">
                                <?php if ($editCategory): ?>
                                    <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="name">Category Name</label>
                                    <input type="text" id="name" name="name" value="<?= $editCategory ? $editCategory['name'] : '' ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" rows="5"><?= $editCategory ? $editCategory['description'] : '' ?></textarea>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="save_category" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?= $editCategory ? 'Update Category' : 'Add Category' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Categories Table -->
                <div class="admin-card">
                    <div class="card-header">
                        <h2><i class="fas fa-folder"></i> All Categories</h2>
                        <div class="header-actions">
                            <span class="item-count"><?= $totalCategories ?> categories</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Videos</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categories)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No categories found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><?= $category['id'] ?></td>
                                                <td><?= $category['name'] ?></td>
                                                <td><?= $category['description'] ? substr($category['description'], 0, 100) . (strlen($category['description']) > 100 ? '...' : '') : 'No description' ?></td>
                                                <td><?= getCategoryVideoCount($category['name']) ?></td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a href="<?= SITE_URL ?>/admin/videos.php?category=<?= urlencode($category['name']) ?>" class="action-btn view" title="View Videos">
                                                            <i class="fas fa-film"></i>
                                                        </a>
                                                        <a href="<?= SITE_URL ?>/admin/categories.php?edit=<?= $category['id'] ?>" class="action-btn edit" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= SITE_URL ?>/admin/categories.php?delete=<?= $category['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this category?')">
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
                                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="pagination-item">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="pagination-item <?= $i === $page ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="pagination-item">
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