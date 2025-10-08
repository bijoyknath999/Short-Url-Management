<?php
/**
 * Configuration File
 * Loads environment variables and defines constants
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Load .env file
$envPath = __DIR__ . '/../.env';
if (!file_exists($envPath)) {
    $envPath = __DIR__ . '/../.env.example';
}
loadEnv($envPath);

// Define constants
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: '@4321bkna');
define('ADMIN_KEY', getenv('ADMIN_KEY') ?: 'your_secret_api_key_here');
define('TELEGRAM_BOT_TOKEN', getenv('TELEGRAM_BOT_TOKEN') ?: '');
define('TELEGRAM_CHAT_ID', getenv('TELEGRAM_CHAT_ID') ?: '');
// Auto-detect BASE_URL if not set
$defaultBaseUrl = 'http://localhost';
if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $defaultBaseUrl = $protocol . '://' . $host . $scriptPath;
    $defaultBaseUrl = rtrim($defaultBaseUrl, '/');
}
define('BASE_URL', getenv('BASE_URL') ?: $defaultBaseUrl);
define('DB_PATH', __DIR__ . '/../' . (getenv('DB_PATH') ?: 'data/shortenv.db'));
define('SESSION_LIFETIME', (int)(getenv('SESSION_LIFETIME') ?: 86400));

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('UTC');
