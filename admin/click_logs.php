<?php
/**
 * Click Logs Viewer
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

// Get filter parameters
$code = $_GET['code'] ?? null;
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Get click logs
$clicks = getClickLogs($code, $perPage, $offset);
$totalClicks = getTotalClicksCount($code);
$totalPages = ceil($totalClicks / $perPage);

// Get URL info if filtering by code
$urlInfo = null;
if ($code) {
    $urlInfo = getShortUrl($code);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click Logs - Short URL System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>📊 Click Logs</h1>
            <div>
                <?php if ($code): ?>
                    <a href="click_logs.php" class="btn btn-secondary">View All Logs</a>
                <?php endif; ?>
                <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
        </div>
        
        <?php if ($urlInfo): ?>
            <div class="url-info-box">
                <h3>Filtering by: <code><?php echo e($code); ?></code></h3>
                <p><strong>Target:</strong> <a href="<?php echo e($urlInfo['target']); ?>" target="_blank"><?php echo e($urlInfo['target']); ?></a></p>
                <p><strong>Total Clicks:</strong> <?php echo number_format($urlInfo['clicks']); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="stats-summary">
            <p>Showing <?php echo number_format(count($clicks)); ?> of <?php echo number_format($totalClicks); ?> click records</p>
        </div>
        
        <div class="table-container">
            <?php if (empty($clicks)): ?>
                <div class="empty-state">
                    <p>No click logs found.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Target URL</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Referer</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clicks as $click): ?>
                            <tr>
                                <td>
                                    <code class="code-badge"><?php echo e($click['code']); ?></code>
                                    <?php if (!$code): ?>
                                        <a href="?code=<?php echo e($click['code']); ?>" 
                                           class="filter-link"
                                           title="Filter by this code">🔍</a>
                                    <?php endif; ?>
                                </td>
                                <td class="url-cell">
                                    <a href="<?php echo e($click['target']); ?>" 
                                       target="_blank" 
                                       class="target-url"
                                       title="<?php echo e($click['target']); ?>">
                                        <?php echo e(substr($click['target'], 0, 40)) . (strlen($click['target']) > 40 ? '...' : ''); ?>
                                    </a>
                                </td>
                                <td><code><?php echo e($click['ip']); ?></code></td>
                                <td class="ua-cell" title="<?php echo e($click['user_agent']); ?>">
                                    <?php echo e($click['user_agent']); ?>
                                </td>
                                <td class="referer-cell">
                                    <?php if (!empty($click['referer'])): ?>
                                        <a href="<?php echo e($click['referer']); ?>" 
                                           target="_blank"
                                           title="<?php echo e($click['referer']); ?>">
                                            <?php echo e(substr($click['referer'], 0, 30)) . (strlen($click['referer']) > 30 ? '...' : ''); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Direct</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo formatDate($click['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                               class="btn btn-secondary">← Previous</a>
                        <?php endif; ?>
                        
                        <span class="page-info">
                            Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                        </span>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                               class="btn btn-secondary">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
