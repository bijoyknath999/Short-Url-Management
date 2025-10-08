<?php
/**
 * API Endpoints
 * /api/create - Create short URL
 * /api/delete - Delete short URL
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get authorization header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (empty($authHeader) && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? '';
}

// Verify Bearer token
$token = '';
if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
    $token = $matches[1];
}

if ($token !== ADMIN_KEY) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized. Invalid or missing API key.'
    ]);
    exit;
}

// Get request body
$input = json_decode(file_get_contents('php://input'), true);

// Route to appropriate handler
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreate($input);
        break;
    
    case 'delete':
        handleDelete($input);
        break;
    
    case 'list':
        handleList($input);
        break;
    
    case 'stats':
        handleStats();
        break;
    
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action. Available actions: create, delete, list, stats'
        ]);
        break;
}

/**
 * Handle create action
 */
function handleCreate($input) {
    $target = trim($input['target'] ?? '');
    $code = trim($input['code'] ?? '');
    $redirectType = (int)($input['redirect_type'] ?? 302);
    $autoGenerate = $input['auto_generate'] ?? false;
    
    // Validate target URL
    if (empty($target)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Target URL is required'
        ]);
        return;
    }
    
    if (!isValidUrl($target)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid target URL. Must be a valid HTTP or HTTPS URL'
        ]);
        return;
    }
    
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
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid code format. Use only letters, numbers, hyphens, and underscores (3-50 characters)'
            ]);
            return;
        }
        
        if (codeExists($code)) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'error' => 'Code already exists'
            ]);
            return;
        }
    }
    
    // Validate redirect type
    if (!in_array($redirectType, [301, 302])) {
        $redirectType = 302;
    }
    
    // Create short URL
    if (createShortUrl($code, $target, $redirectType)) {
        $shortUrl = BASE_URL . '/' . $code;
        
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'data' => [
                'code' => $code,
                'target' => $target,
                'short_url' => $shortUrl,
                'redirect_type' => $redirectType
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create short URL'
        ]);
    }
}

/**
 * Handle delete action
 */
function handleDelete($input) {
    $code = trim($input['code'] ?? '');
    
    if (empty($code)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Code is required'
        ]);
        return;
    }
    
    // Get URL by code
    $url = getShortUrl($code);
    
    if (!$url) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Short URL not found'
        ]);
        return;
    }
    
    // Delete short URL
    if (deleteShortUrl($url['id'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Short URL deleted successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete short URL'
        ]);
    }
}

/**
 * Handle list action
 */
function handleList($input) {
    $search = $input['search'] ?? '';
    $orderBy = $input['order_by'] ?? 'created_at';
    $orderDir = $input['order_dir'] ?? 'DESC';
    
    $urls = getAllShortUrls($search, $orderBy, $orderDir);
    
    // Format URLs for API response
    $formattedUrls = array_map(function($url) {
        return [
            'id' => $url['id'],
            'code' => $url['code'],
            'target' => $url['target'],
            'short_url' => BASE_URL . '/' . $url['code'],
            'redirect_type' => $url['redirect_type'],
            'clicks' => (int)$url['clicks'],
            'last_click_at' => $url['last_click_at'],
            'created_at' => $url['created_at'],
            'updated_at' => $url['updated_at']
        ];
    }, $urls);
    
    echo json_encode([
        'success' => true,
        'data' => $formattedUrls,
        'count' => count($formattedUrls)
    ]);
}

/**
 * Handle stats action
 */
function handleStats() {
    $db = getDB();
    
    $totalUrls = $db->query("SELECT COUNT(*) FROM short_urls")->fetchColumn();
    $totalClicks = $db->query("SELECT SUM(clicks) FROM short_urls")->fetchColumn() ?: 0;
    $totalClickRecords = $db->query("SELECT COUNT(*) FROM clicks")->fetchColumn();
    
    // Get top URLs
    $stmt = $db->query("SELECT code, target, clicks FROM short_urls ORDER BY clicks DESC LIMIT 10");
    $topUrls = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_urls' => (int)$totalUrls,
            'total_clicks' => (int)$totalClicks,
            'total_click_records' => (int)$totalClickRecords,
            'average_clicks_per_url' => $totalUrls > 0 ? round($totalClicks / $totalUrls, 2) : 0,
            'top_urls' => $topUrls
        ]
    ]);
}
