<?php
/**
 * Admin Dashboard
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

// Get search and sort parameters
$search = $_GET['search'] ?? '';
$orderBy = $_GET['order_by'] ?? 'created_at';
$orderDir = $_GET['order_dir'] ?? 'DESC';
$dateFilter = $_GET['date'] ?? date('Y-m-d'); // Default to today

// Get all short URLs with date filter
$db = getDB();

if (!empty($dateFilter)) {
    $stmt = $db->prepare("
        SELECT * FROM short_urls 
        WHERE DATE(created_at) = ? 
        AND (code LIKE ? OR target LIKE ?)
        ORDER BY $orderBy $orderDir
    ");
    $searchTerm = '%' . $search . '%';
    $stmt->execute([$dateFilter, $searchTerm, $searchTerm]);
    $urls = $stmt->fetchAll();
} else {
    $urls = getAllShortUrls($search, $orderBy, $orderDir);
}

// Get statistics (filtered by date if selected)
if (!empty($dateFilter)) {
    $totalUrls = $db->prepare("SELECT COUNT(*) FROM short_urls WHERE DATE(created_at) = ?");
    $totalUrls->execute([$dateFilter]);
    $totalUrls = $totalUrls->fetchColumn();
    
    $totalClicksStmt = $db->prepare("SELECT SUM(clicks) FROM short_urls WHERE DATE(created_at) = ?");
    $totalClicksStmt->execute([$dateFilter]);
    $totalClicks = $totalClicksStmt->fetchColumn() ?: 0;
    
    $totalClickRecordsStmt = $db->prepare("SELECT COUNT(*) FROM clicks WHERE DATE(created_at) = ?");
    $totalClickRecordsStmt->execute([$dateFilter]);
    $totalClickRecords = $totalClickRecordsStmt->fetchColumn();
} else {
    $totalUrls = $db->query("SELECT COUNT(*) FROM short_urls")->fetchColumn();
    $totalClicks = $db->query("SELECT SUM(clicks) FROM short_urls")->fetchColumn() ?: 0;
    $totalClickRecords = $db->query("SELECT COUNT(*) FROM clicks")->fetchColumn();
}

// Toggle sort direction
$newOrderDir = $orderDir === 'ASC' ? 'DESC' : 'ASC';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Short URL System</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1.2">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <div>
                <h1>📊 Dashboard</h1>
                <?php if (!empty($dateFilter)): ?>
                    <p style="margin-top: 0.5rem; color: #6b7280; font-size: 0.875rem;">
                        📅 Showing data for: <strong><?php echo date('F d, Y', strtotime($dateFilter)); ?></strong>
                    </p>
                <?php endif; ?>
            </div>
            <a href="create.php" class="btn btn-primary">+ Create Short URL</a>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🔗</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($totalUrls); ?></div>
                    <div class="stat-label">Total URLs</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👆</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($totalClicks); ?></div>
                    <div class="stat-label">Unique Visitors</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo number_format($totalClickRecords); ?></div>
                    <div class="stat-label">Click Records</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo $totalUrls > 0 ? number_format($totalClicks / $totalUrls, 1) : '0'; ?></div>
                    <div class="stat-label">Avg Clicks/URL</div>
                </div>
            </div>
        </div>
        
        <!-- Search and Filter -->
        <div class="toolbar">
            <form method="GET" action="" class="search-form">
                <input 
                    type="date" 
                    name="date" 
                    value="<?php echo e($dateFilter); ?>"
                    class="search-input date-input"
                >
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by code or URL..." 
                    value="<?php echo e($search); ?>"
                    class="search-input"
                >
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="dashboard.php?date=" class="btn btn-secondary">Show All</a>
            </form>
            
            <a href="click_logs.php" class="btn btn-secondary">📊 View Click Logs</a>
        </div>
        
        <!-- URLs Table -->
        <div class="table-container">
            <?php if (empty($urls)): ?>
                <div class="empty-state">
                    <p>No short URLs found.</p>
                    <a href="create.php" class="btn btn-primary">Create Your First Short URL</a>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>&order_by=code&order_dir=<?php echo $newOrderDir; ?>">
                                    Code <?php echo $orderBy === 'code' ? ($orderDir === 'ASC' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>&order_by=target&order_dir=<?php echo $newOrderDir; ?>">
                                    Target URL <?php echo $orderBy === 'target' ? ($orderDir === 'ASC' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>&order_by=clicks&order_dir=<?php echo $newOrderDir; ?>">
                                    Unique Clicks <?php echo $orderBy === 'clicks' ? ($orderDir === 'ASC' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>Type</th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>&order_by=last_click_at&order_dir=<?php echo $newOrderDir; ?>">
                                    Last Click <?php echo $orderBy === 'last_click_at' ? ($orderDir === 'ASC' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($dateFilter); ?>&order_by=created_at&order_dir=<?php echo $newOrderDir; ?>">
                                    Created <?php echo $orderBy === 'created_at' ? ($orderDir === 'ASC' ? '↑' : '↓') : ''; ?>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($urls as $url): ?>
                            <tr>
                                <td>
                                    <code class="code-badge"><?php echo e($url['code']); ?></code>
                                    <a href="<?php echo BASE_URL . '/' . e($url['code']); ?>" 
                                       target="_blank" 
                                       class="external-link"
                                       title="Open short URL">🔗</a>
                                </td>
                                <td class="url-cell">
                                    <a href="<?php echo e($url['target']); ?>" 
                                       target="_blank" 
                                       class="target-url"
                                       title="<?php echo e($url['target']); ?>">
                                        <?php echo e(substr($url['target'], 0, 60)) . (strlen($url['target']) > 60 ? '...' : ''); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?php echo number_format($url['clicks']); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $url['redirect_type'] == 301 ? 'success' : 'warning'; ?>">
                                        <?php echo $url['redirect_type']; ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($url['last_click_at']); ?></td>
                                <td><?php echo formatDate($url['created_at']); ?></td>
                                <td class="actions-cell">
                                    <a href="edit.php?id=<?php echo $url['id']; ?>" 
                                       class="btn btn-sm btn-secondary"
                                       title="Edit">✏️</a>
                                    <a href="click_logs.php?code=<?php echo e($url['code']); ?>" 
                                       class="btn btn-sm btn-secondary"
                                       title="View Clicks">📊</a>
                                    <a href="delete.php?id=<?php echo $url['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this short URL?')"
                                       title="Delete">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
