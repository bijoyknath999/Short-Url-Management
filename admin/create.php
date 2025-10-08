<?php
/**
 * Create Short URL
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target = trim($_POST['target'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $redirectType = (int)($_POST['redirect_type'] ?? 302);
    $autoGenerate = isset($_POST['auto_generate']);
    
    // Validate target URL
    if (empty($target)) {
        $error = 'Target URL is required';
    } elseif (!isValidUrl($target)) {
        $error = 'Invalid target URL. Must be a valid HTTP or HTTPS URL';
    } else {
        $target = sanitizeUrl($target);
        
        // Generate or validate code
        if ($autoGenerate || empty($code)) {
            // Generate unique code
            do {
                $code = generateCode(6);
            } while (codeExists($code));
        } else {
            // Validate custom code
            if (!isValidCode($code)) {
                $error = 'Invalid code format. Use only letters, numbers, hyphens, and underscores (3-50 characters)';
            } elseif (codeExists($code)) {
                $error = 'Code already exists. Please choose a different code';
            }
        }
        
        // Validate redirect type
        if (!in_array($redirectType, [301, 302])) {
            $redirectType = 302;
        }
        
        // Create short URL if no errors
        if (empty($error)) {
            if (createShortUrl($code, $target, $redirectType)) {
                $success = 'Short URL created successfully!';
                $shortUrl = BASE_URL . '/' . $code;
            } else {
                $error = 'Failed to create short URL. Please try again';
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
    <title>Create Short URL - Short URL System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>➕ Create Short URL</h1>
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
                <div class="success-details">
                    <strong>Short URL:</strong> 
                    <a href="<?php echo e($shortUrl); ?>" target="_blank"><?php echo e($shortUrl); ?></a>
                    <button onclick="copyToClipboard('<?php echo e($shortUrl); ?>')" class="btn btn-sm btn-secondary">
                        📋 Copy
                    </button>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="" class="form">
                <div class="form-group">
                    <label for="target">Target URL *</label>
                    <input 
                        type="url" 
                        id="target" 
                        name="target" 
                        required 
                        placeholder="https://example.com/very/long/url"
                        value="<?php echo e($_POST['target'] ?? ''); ?>"
                    >
                    <small>The full URL you want to shorten (must start with http:// or https://)</small>
                </div>
                
                <div class="form-group">
                    <label>
                        <input 
                            type="checkbox" 
                            name="auto_generate" 
                            id="auto_generate"
                            onchange="toggleCodeInput()"
                            <?php echo isset($_POST['auto_generate']) ? 'checked' : ''; ?>
                        >
                        Auto-generate code
                    </label>
                </div>
                
                <div class="form-group" id="code-group">
                    <label for="code">Custom Code</label>
                    <input 
                        type="text" 
                        id="code" 
                        name="code" 
                        placeholder="my-custom-code"
                        pattern="[a-zA-Z0-9_-]+"
                        minlength="3"
                        maxlength="50"
                        value="<?php echo e($_POST['code'] ?? ''); ?>"
                    >
                    <small>Optional: Use letters, numbers, hyphens, and underscores (3-50 characters)</small>
                </div>
                
                <div class="form-group">
                    <label for="redirect_type">Redirect Type</label>
                    <select id="redirect_type" name="redirect_type">
                        <option value="302" <?php echo ($_POST['redirect_type'] ?? 302) == 302 ? 'selected' : ''; ?>>
                            302 - Temporary (Default)
                        </option>
                        <option value="301" <?php echo ($_POST['redirect_type'] ?? 302) == 301 ? 'selected' : ''; ?>>
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
                        Create Short URL
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
    <script>
        function toggleCodeInput() {
            const autoGenerate = document.getElementById('auto_generate').checked;
            const codeInput = document.getElementById('code');
            const codeGroup = document.getElementById('code-group');
            
            if (autoGenerate) {
                codeInput.disabled = true;
                codeInput.required = false;
                codeGroup.style.opacity = '0.5';
            } else {
                codeInput.disabled = false;
                codeGroup.style.opacity = '1';
            }
        }
        
        // Initialize on page load
        toggleCodeInput();
    </script>
</body>
</html>
