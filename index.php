<?php
/**
 * Main Redirect Handler
 * Handles /code and /s/code routes
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Get the code from URL
$code = $_GET['code'] ?? '';

if (empty($code)) {
    // No code provided, redirect to admin or show 404
    header('Location: /admin/dashboard.php');
    exit;
}

// Get short URL from database
$url = getShortUrl($code);

if (!$url) {
    // Code not found
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Short URL Not Found</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                color: #fff;
            }
            .error-container {
                text-align: center;
                padding: 2rem;
            }
            .error-code {
                font-size: 8rem;
                font-weight: bold;
                margin-bottom: 1rem;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            }
            h1 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            p {
                font-size: 1.2rem;
                opacity: 0.9;
                margin-bottom: 2rem;
            }
            .btn {
                display: inline-block;
                padding: 12px 24px;
                background: #fff;
                color: #667eea;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                transition: transform 0.2s;
            }
            .btn:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-code">404</div>
            <h1>Short URL Not Found</h1>
            <p>The short URL code "<?php echo htmlspecialchars($code); ?>" does not exist.</p>
            <a href="/" class="btn">Go Home</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Get client information
$ip = getClientIP();
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// Check if this IP already clicked this URL
$db = getDB();
$stmt = $db->prepare("SELECT COUNT(*) FROM clicks WHERE code = ? AND ip = ?");
$stmt->execute([$code, $ip]);
$alreadyClicked = $stmt->fetchColumn() > 0;

// Track the click only if this IP hasn't clicked before
if (!$alreadyClicked) {
    trackClick($code, $url['target'], $ip, $userAgent, $referer);
    
    // Send Telegram notification (async, don't wait for response)
    $telegramMessage = formatClickNotification($code, $url['target'], $ip, $userAgent);
    sendTelegramNotification($telegramMessage);
}

// Perform redirect
$redirectType = (int)$url['redirect_type'];
if ($redirectType === 301) {
    header('HTTP/1.1 301 Moved Permanently');
} else {
    header('HTTP/1.1 302 Found');
}

// Add cache control headers
if ($redirectType === 301) {
    // Cache permanent redirects
    header('Cache-Control: public, max-age=31536000');
} else {
    // Don't cache temporary redirects
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
}

header('Location: ' . $url['target']);
exit;
