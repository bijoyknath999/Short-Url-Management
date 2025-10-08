<?php
/**
 * Edit Short URL
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$id = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';

// Get existing URL
$url = null;
if ($id > 0) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM short_urls WHERE id = ?");
    $stmt->execute([$id]);
    $url = $stmt->fetch();
}

if (!$url) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target = trim($_POST['target'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $redirectType = (int)($_POST['redirect_type'] ?? 302);
    
    // Validate target URL
    if (empty($target)) {
        $error = 'Target URL is required';
    } elseif (!isValidUrl($target)) {
        $error = 'Invalid target URL. Must be a valid HTTP or HTTPS URL';
    } elseif (empty($code)) {
        $error = 'Code is required';
    } elseif (!isValidCode($code)) {
        $error = 'Invalid code format. Use only letters, numbers, hyphens, and underscores (3-50 characters)';
    } else {
        $target = sanitizeUrl($target);
        
        // Check if code exists for different URL
        if ($code !== $url['code']) {
            if (codeExists($code)) {
                $error = 'Code already exists. Please choose a different code';
            }
        }
        
        // Validate redirect type
        if (!in_array($redirectType, [301, 302])) {
            $redirectType = 302;
        }
        
        // Update short URL if no errors
        if (empty($error)) {
            if (updateShortUrl($id, $code, $target, $redirectType)) {
                $success = 'Short URL updated successfully!';
                // Refresh URL data
                $stmt = $db->prepare("SELECT * FROM short_urls WHERE id = ?");
                $stmt->execute([$id]);
                $url = $stmt->fetch();
            } else {
                $error = 'Failed to update short URL. Please try again';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Short URL - Short URL System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>✏️ Edit Short URL</h1>
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo e($success); ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <div class="url-info">
                <h3>Current Short URL</h3>
                <p>
                    <a href="<?php echo BASE_URL . '/' . e($url['code']); ?>" target="_blank">
                        <?php echo BASE_URL . '/' . e($url['code']); ?>
                    </a>
                </p>
                <div class="url-stats">
                    <span><strong>Clicks:</strong> <?php echo number_format($url['clicks']); ?></span>
                    <span><strong>Created:</strong> <?php echo formatDate($url['created_at']); ?></span>
                    <span><strong>Last Click:</strong> <?php echo formatDate($url['last_click_at']); ?></span>
                </div>
            </div>
            
            <form method="POST" action="" class="form">
                <div class="form-group">
                    <label for="target">Target URL *</label>
                    <input 
                        type="url" 
                        id="target" 
                        name="target" 
                        required 
                        placeholder="https://example.com/very/long/url"
                        value="<?php echo e($_POST['target'] ?? $url['target']); ?>"
                    >
                    <small>The full URL you want to shorten (must start with http:// or https://)</small>
                </div>
                
                <div class="form-group">
                    <label for="code">Code *</label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        required
                        placeholder="my-custom-code"
                        pattern="[a-zA-Z0-9_-]+"
                        minlength="3"
                        maxlength="50"
                        value="<?php echo e($_POST['code'] ?? $url['code']); ?>"
                    >
                    <small>Use letters, numbers, hyphens, and underscores (3-50 characters)</small>
                </div>
                
                <div class="form-group">
                    <label for="redirect_type">Redirect Type</label>
                    <select id="redirect_type" name="redirect_type">
                        <option value="302" <?php echo ($_POST['redirect_type'] ?? $url['redirect_type']) == 302 ? 'selected' : ''; ?>>
                            302 - Temporary (Default)
                        </option>
                        <option value="301" <?php echo ($_POST['redirect_type'] ?? $url['redirect_type']) == 301 ? 'selected' : ''; ?>>
                            301 - Permanent
                        </option>
                    </select>
                    <small>
                        302: Temporary redirect (can be changed later)<br>
                        301: Permanent redirect (cached by browsers)
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Update Short URL
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        Cancel
                    </a>
                    <a href="delete.php?id=<?php echo $url['id']; ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this short URL?')">
                        Delete
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
