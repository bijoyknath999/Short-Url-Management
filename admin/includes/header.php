<?php
/**
 * Admin Header Navigation
 */
?>
<header class="admin-header">
    <div class="header-container">
        <div class="header-left">
            <a href="dashboard.php" class="logo">
                🔗 Short URL System
            </a>
        </div>
        
        <nav class="header-nav">
            <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                Dashboard
            </a>
            <a href="create.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'create.php' ? 'active' : ''; ?>">
                Create
            </a>
            <a href="click_logs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'click_logs.php' ? 'active' : ''; ?>">
                Click Logs
            </a>
            <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                ⚙️ Settings
            </a>
        </nav>
        
        <div class="header-right">
            <span class="user-info">👤 Admin</span>
            <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>
</header>
