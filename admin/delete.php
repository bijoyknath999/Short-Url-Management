<?php
/**
 * Delete Short URL
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    if (deleteShortUrl($id)) {
        $_SESSION['flash_message'] = 'Short URL deleted successfully';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'Failed to delete short URL';
        $_SESSION['flash_type'] = 'error';
    }
}

header('Location: dashboard.php');
exit;
