<?php
/**
 * Admin Login Page
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already authenticated
if (isAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    if ($password === ADMIN_PASSWORD) {
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['login_time'] = time();
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Short URL System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🔗 Short URL System</h1>
                <p>Admin Login</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo e($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        autofocus
                        placeholder="Enter admin password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Login
                </button>
            </form>
            
            <div class="login-footer">
                <p>Short URL Management System v1.0</p>
            </div>
        </div>
    </div>
</body>
</html>
