<?php
/**
 * Reusable Functions
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Validate URL
 */
function isValidUrl($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    $scheme = parse_url($url, PHP_URL_SCHEME);
    return in_array($scheme, ['http', 'https']);
}

/**
 * Sanitize URL
 */
function sanitizeUrl($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

/**
 * Generate random code
 */
function generateCode($length = 6) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    $max = strlen($characters) - 1;
    
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, $max)];
    }
    
    return $code;
}

/**
 * Check if code exists
 */
function codeExists($code) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM short_urls WHERE code = ?");
    $stmt->execute([$code]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Get short URL by code
 */
function getShortUrl($code) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM short_urls WHERE code = ?");
    $stmt->execute([$code]);
    return $stmt->fetch();
}

/**
 * Get all short URLs
 */
function getAllShortUrls($search = '', $orderBy = 'created_at', $orderDir = 'DESC') {
    $db = getDB();
    
    $allowedColumns = ['code', 'target', 'clicks', 'created_at', 'last_click_at'];
    if (!in_array($orderBy, $allowedColumns)) {
        $orderBy = 'created_at';
    }
    
    $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
    
    if (!empty($search)) {
        $stmt = $db->prepare("
            SELECT * FROM short_urls 
            WHERE code LIKE ? OR target LIKE ?
            ORDER BY $orderBy $orderDir
        ");
        $searchTerm = '%' . $search . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
    } else {
        $stmt = $db->query("SELECT * FROM short_urls ORDER BY $orderBy $orderDir");
    }
    
    return $stmt->fetchAll();
}

/**
 * Create short URL
 */
function createShortUrl($code, $target, $redirectType = 302) {
    $db = getDB();
    $now = date('Y-m-d H:i:s');
    
    $stmt = $db->prepare("
        INSERT INTO short_urls (code, target, redirect_type, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    return $stmt->execute([$code, $target, $redirectType, $now, $now]);
}

/**
 * Update short URL
 */
function updateShortUrl($id, $code, $target, $redirectType = 302) {
    $db = getDB();
    $now = date('Y-m-d H:i:s');
    
    $stmt = $db->prepare("
        UPDATE short_urls 
        SET code = ?, target = ?, redirect_type = ?, updated_at = ?
        WHERE id = ?
    ");
    
    return $stmt->execute([$code, $target, $redirectType, $now, $id]);
}

/**
 * Delete short URL
 */
function deleteShortUrl($id) {
    $db = getDB();
    
    // Get code first to delete associated clicks
    $stmt = $db->prepare("SELECT code FROM short_urls WHERE id = ?");
    $stmt->execute([$id]);
    $url = $stmt->fetch();
    
    if ($url) {
        // Delete associated clicks
        $stmt = $db->prepare("DELETE FROM clicks WHERE code = ?");
        $stmt->execute([$url['code']]);
        
        // Delete short URL
        $stmt = $db->prepare("DELETE FROM short_urls WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    return false;
}

/**
 * Track click
 */
function trackClick($code, $target, $ip, $userAgent, $referer = '') {
    $db = getDB();
    $now = date('Y-m-d H:i:s');
    
    // Insert click record
    $stmt = $db->prepare("
        INSERT INTO clicks (code, target, ip, user_agent, referer, created_at)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$code, $target, $ip, $userAgent, $referer, $now]);
    
    // Update short URL clicks count and last click time
    $stmt = $db->prepare("
        UPDATE short_urls 
        SET clicks = clicks + 1, last_click_at = ?
        WHERE code = ?
    ");
    $stmt->execute([$now, $code]);
}

/**
 * Get click logs
 */
function getClickLogs($code = null, $limit = 100, $offset = 0) {
    $db = getDB();
    
    if ($code) {
        $stmt = $db->prepare("
            SELECT * FROM clicks 
            WHERE code = ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$code, $limit, $offset]);
    } else {
        $stmt = $db->prepare("
            SELECT * FROM clicks 
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
    }
    
    return $stmt->fetchAll();
}

/**
 * Get total clicks count
 */
function getTotalClicksCount($code = null) {
    $db = getDB();
    
    if ($code) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM clicks WHERE code = ?");
        $stmt->execute([$code]);
    } else {
        $stmt = $db->query("SELECT COUNT(*) FROM clicks");
    }
    
    return $stmt->fetchColumn();
}

/**
 * Send Telegram notification
 * 
 * @param string $message HTML formatted message to send
 * @param string|null $customToken Optional override for bot token (defaults to TELEGRAM_BOT_TOKEN)
 * @param string|null $customChatId Optional override for chat ID (defaults to TELEGRAM_CHAT_ID)
 * @param string|null &$errorMsg Output parameter for failure reason
 * @return bool True on success, false on failure
 */
function sendTelegramNotification($message, $customToken = null, $customChatId = null, &$errorMsg = null) {
    $botToken = $customToken ?: TELEGRAM_BOT_TOKEN;
    $chatId = $customChatId ?: TELEGRAM_CHAT_ID;

    if (empty($botToken) || empty($chatId) || 
        $botToken === 'your_bot_token_here' || $chatId === 'your_chat_id_here') {
        $errorMsg = 'Bot Token or Chat ID is missing or set to placeholder.';
        return false;
    }
    
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    // Use cURL if available (recommended for reliability, cPanel, and SSL support)
    if (function_exists('curl_init') && extension_loaded('curl')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);
        
        if ($result !== false && $httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        $decoded = json_decode($result, true);
        if (!empty($decoded['description'])) {
            $errorMsg = "Telegram API error ({$httpCode}): " . $decoded['description'];
        } elseif ($curlErr) {
            $errorMsg = "cURL error: " . $curlErr;
        } else {
            $errorMsg = "HTTP error {$httpCode}: " . substr($result, 0, 200);
        }
        return false;
    }
    
    // Fallback to stream context / file_get_contents if curl is not installed
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'timeout' => 8,
            'ignore_errors' => true
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result !== false) {
        $decoded = json_decode($result, true);
        if (isset($decoded['ok']) && $decoded['ok'] === true) {
            return true;
        }
        if (!empty($decoded['description'])) {
            $errorMsg = "Telegram error: " . $decoded['description'];
            return false;
        }
        return true;
    }

    $lastErr = error_get_last();
    $errorMsg = $lastErr['message'] ?? 'Connection failed. Please verify network access and PHP configuration.';
    return false;
}

/**
 * Format click notification for Telegram
 */
function formatClickNotification($code, $target, $ip, $userAgent) {
    $timestamp = date('Y-m-d H:i:s');
    $shortUrl = BASE_URL . '/' . $code;
    
    // Escape user agent for HTML but keep full length
    $escapedUA = htmlspecialchars($userAgent);
    
    return "🔗 <b>Short redirect:</b> {$code}\n" .
           "→ {$target}\n\n" .
           "📍 <b>IP:</b> <code>{$ip}</code>\n" .
           "🖥 <b>UA:</b> <code>{$escapedUA}</code>\n" .
           "⏰ <b>Time:</b> {$timestamp}";
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ipKeys = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Handle comma-separated IPs (X-Forwarded-For)
            if (strpos($ip, ',') !== false) {
                $ips = explode(',', $ip);
                $ip = trim($ips[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    
    return '0.0.0.0';
}

/**
 * Escape HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display
 */
function formatDate($date) {
    if (empty($date)) {
        return 'Never';
    }
    return date('M d, Y H:i', strtotime($date));
}

/**
 * Validate code format
 */
function isValidCode($code) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $code) && strlen($code) >= 3 && strlen($code) <= 50;
}
