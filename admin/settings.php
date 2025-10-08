<?php
/**
 * Admin Settings - Telegram Configuration
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $botToken = trim($_POST['bot_token'] ?? '');
    $chatId = trim($_POST['chat_id'] ?? '');
    
    // Read current .env file
    $envPath = __DIR__ . '/../.env';
    if (!file_exists($envPath)) {
        $envPath = __DIR__ . '/../.env.example';
    }
    
    $envContent = file_get_contents($envPath);
    
    // Update Telegram settings
    $envContent = preg_replace('/TELEGRAM_BOT_TOKEN=.*/', 'TELEGRAM_BOT_TOKEN=' . $botToken, $envContent);
    $envContent = preg_replace('/TELEGRAM_CHAT_ID=.*/', 'TELEGRAM_CHAT_ID=' . $chatId, $envContent);
    
    // Save to .env
    $envPath = __DIR__ . '/../.env';
    if (file_put_contents($envPath, $envContent)) {
        $success = 'Telegram settings saved successfully! Reload the page to apply changes.';
        
        // Test notification if both values provided
        if (!empty($botToken) && !empty($chatId)) {
            $testMessage = "🎉 <b>Telegram Integration Active!</b>\n\n" .
                          "Your Short URL system is now connected to Telegram.\n" .
                          "You'll receive notifications here for every redirect.\n\n" .
                          "⏰ <b>Time:</b> " . date('Y-m-d H:i:s');
            
            $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
            $data = [
                'chat_id' => $chatId,
                'text' => $testMessage,
                'parse_mode' => 'HTML'
            ];
            
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                    'timeout' => 5
                ]
            ];
            
            $context = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);
            
            if ($result) {
                $success .= ' Test notification sent to Telegram!';
            } else {
                $error = 'Settings saved but failed to send test notification. Please check your Bot Token and Chat ID.';
            }
        }
    } else {
        $error = 'Failed to save settings. Please check file permissions.';
    }
}

// Get current values
$currentBotToken = TELEGRAM_BOT_TOKEN;
$currentChatId = TELEGRAM_CHAT_ID;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Settings - Short URL System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>⚙️ Telegram Settings</h1>
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
            <div class="info-box" style="background: #dbeafe; border-left: 4px solid #3b82f6; padding: 1rem; margin-bottom: 2rem; border-radius: 8px;">
                <h3 style="margin-bottom: 0.5rem;">📱 How to Set Up Telegram Notifications</h3>
                <ol style="margin: 0.5rem 0; padding-left: 1.5rem;">
                    <li><strong>Create a Bot:</strong> Message <a href="https://t.me/botfather" target="_blank">@BotFather</a> on Telegram</li>
                    <li>Send <code>/newbot</code> and follow instructions</li>
                    <li>Copy the <strong>Bot Token</strong> (looks like: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz)</li>
                    <li><strong>Get Your Chat ID:</strong> Message <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a></li>
                    <li>Copy your <strong>Chat ID</strong> (a number like: 123456789)</li>
                    <li>Paste both values below and click Save</li>
                </ol>
            </div>
            
            <form method="POST" action="" class="form">
                <div class="form-group">
                    <label for="bot_token">Telegram Bot Token</label>
                    <input 
                        type="text" 
                        id="bot_token" 
                        name="bot_token" 
                        placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz"
                        value="<?php echo e($currentBotToken); ?>"
                    >
                    <small>Get this from <a href="https://t.me/botfather" target="_blank">@BotFather</a> on Telegram</small>
                </div>
                
                <div class="form-group">
                    <label for="chat_id">Telegram Chat ID</label>
                    <input 
                        type="text" 
                        id="chat_id" 
                        name="chat_id" 
                        placeholder="123456789"
                        value="<?php echo e($currentChatId); ?>"
                    >
                    <small>Get this from <a href="https://t.me/userinfobot" target="_blank">@userinfobot</a> on Telegram</small>
                </div>
                
                <div class="form-group">
                    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; border-radius: 8px;">
                        <strong>⚠️ Note:</strong> After saving, a test notification will be sent to your Telegram. 
                        Make sure you've started a conversation with your bot first!
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        💾 Save Telegram Settings
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
            
            <div style="margin-top: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                <h3>📊 Current Status</h3>
                <p><strong>Bot Token:</strong> <?php echo empty($currentBotToken) ? '❌ Not configured' : '✅ Configured'; ?></p>
                <p><strong>Chat ID:</strong> <?php echo empty($currentChatId) ? '❌ Not configured' : '✅ Configured'; ?></p>
                <p><strong>Notifications:</strong> <?php echo (empty($currentBotToken) || empty($currentChatId)) ? '❌ Disabled' : '✅ Active'; ?></p>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>
