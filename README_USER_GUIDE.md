# 🔗 Short URL Management System

A modern, feature-rich PHP-based URL shortener with admin dashboard, Telegram integration, click tracking, and comprehensive API.

## ✨ Features

### 🎯 Core Features
- **URL Shortening**: Create short, memorable URLs from long links
- **Custom Codes**: Use auto-generated or custom short codes
- **Redirect Types**: Support for 301 (permanent) and 302 (temporary) redirects
- **Click Tracking**: Detailed analytics for every click
- **Modern UI**: Beautiful, responsive admin dashboard

### 🔐 Admin Panel
- **Secure Login**: Password-protected admin access
- **Dashboard**: Overview with statistics and URL management
- **CRUD Operations**: Create, read, update, and delete short URLs
- **Search & Filter**: Find URLs quickly
- **Click Logs**: View detailed click history

### 📊 Analytics
- **Click Counter**: Track total clicks per URL
- **Click Details**: IP address, user agent, referer, timestamp
- **Statistics**: Total URLs, clicks, and averages
- **Top URLs**: See most popular short links

### 📱 Telegram Integration
- **Real-time Notifications**: Get notified on every redirect
- **Click Details**: IP, user agent, and timestamp in Telegram
- **Easy Setup**: Configure with bot token and chat ID

### 🚀 API Endpoints
- **RESTful API**: JSON-based API for automation
- **Bearer Authentication**: Secure API access
- **CRUD Operations**: Create, list, delete via API
- **Statistics**: Get system stats programmatically

## 📁 Project Structure

```
short-url-system/
├── index.php              # Main redirect handler
├── api.php                # API endpoints
├── .htaccess              # URL rewriting rules
├── .env.example           # Environment variables template
├── admin/
│   ├── dashboard.php      # Admin dashboard
│   ├── login.php          # Admin login
│   ├── logout.php         # Admin logout
│   ├── create.php         # Create short URLs
│   ├── edit.php           # Edit URLs
│   ├── delete.php         # Delete URLs
│   ├── click_logs.php     # View click logs
│   └── includes/
│       └── header.php     # Admin header navigation
├── includes/
│   ├── config.php         # Configuration & environment
│   ├── db.php             # Database connection
│   └── functions.php      # Reusable functions
├── assets/
│   ├── css/
│   │   └── style.css      # Modern UI styles
│   └── js/
│       └── script.js      # JavaScript utilities
└── data/
    └── shortenv.db        # SQLite database (auto-created)
```

## 🚀 Installation

### Requirements
- PHP 7.4 or higher
- SQLite3 extension enabled
- Apache with mod_rewrite (or Nginx with URL rewriting)
- Write permissions for `data/` directory

### Step 1: Upload Files
Upload all files to your web server (e.g., `/var/www/html/` or `public_html/`)

### Step 2: Configure Environment
1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your settings:
   ```env
   # Admin Configuration
   ADMIN_PASSWORD=@4321bkna
   ADMIN_KEY=your_secret_api_key_here
   
   # Telegram Configuration (optional)
   TELEGRAM_BOT_TOKEN=your_bot_token_here
   TELEGRAM_CHAT_ID=your_chat_id_here
   
   # Base URL (without trailing slash)
   BASE_URL=https://yourdomain.com
   
   # Database Path
   DB_PATH=data/shortenv.db
   ```

### Step 3: Set Permissions
```bash
chmod 755 data/
chmod 666 data/shortenv.db  # After first run
```

### Step 4: Configure Web Server

#### Apache (.htaccess included)
The `.htaccess` file is already configured. Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx
Add this to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?code=$uri&$args;
}

location ~ ^/s/(.+)$ {
    try_files $uri /index.php?code=$1;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}

location ^~ /data/ {
    deny all;
}
```

### Step 5: Access Admin Panel
1. Navigate to `https://yourdomain.com/admin/login.php`
2. Login with password: `@4321bkna` (or your custom password)
3. Start creating short URLs!

## 🔧 Configuration

### Admin Password
Change the admin password in `.env`:
```env
ADMIN_PASSWORD=your_secure_password
```

### Telegram Setup
1. Create a bot with [@BotFather](https://t.me/botfather)
2. Get your bot token
3. Get your chat ID (use [@userinfobot](https://t.me/userinfobot))
4. Add to `.env`:
   ```env
   TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
   TELEGRAM_CHAT_ID=123456789
   ```

### API Key
Generate a secure API key for API access:
```bash
openssl rand -hex 32
```
Add to `.env`:
```env
ADMIN_KEY=your_generated_api_key
```

## 📖 Usage

### Creating Short URLs

#### Via Admin Panel
1. Login to admin panel
2. Click "Create Short URL"
3. Enter target URL
4. Choose auto-generate or custom code
5. Select redirect type (301 or 302)
6. Click "Create"

#### Via API
```bash
curl -X POST https://yourdomain.com/api.php?action=create \
  -H "Authorization: Bearer your_api_key" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "https://example.com/very/long/url",
    "code": "custom",
    "redirect_type": 302
  }'
```

### URL Formats
Short URLs work with both formats:
- `https://yourdomain.com/code`
- `https://yourdomain.com/s/code`

### API Endpoints

#### Create Short URL
```bash
POST /api.php?action=create
Authorization: Bearer {ADMIN_KEY}
Content-Type: application/json

{
  "target": "https://example.com/long-url",
  "code": "custom-code",  // Optional
  "redirect_type": 302,    // Optional (301 or 302)
  "auto_generate": false   // Optional
}
```

#### List All URLs
```bash
GET /api.php?action=list
Authorization: Bearer {ADMIN_KEY}

Optional query params:
- search: Search term
- order_by: code|target|clicks|created_at
- order_dir: ASC|DESC
```

#### Delete Short URL
```bash
POST /api.php?action=delete
Authorization: Bearer {ADMIN_KEY}
Content-Type: application/json

{
  "code": "short-code"
}
```

#### Get Statistics
```bash
GET /api.php?action=stats
Authorization: Bearer {ADMIN_KEY}
```

### API Response Format
```json
{
  "success": true,
  "data": {
    "code": "abc123",
    "target": "https://example.com",
    "short_url": "https://yourdomain.com/abc123",
    "redirect_type": 302
  }
}
```

## 🗄️ Database Schema

### short_urls Table
```sql
CREATE TABLE short_urls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    target TEXT NOT NULL,
    redirect_type INTEGER DEFAULT 302,
    clicks INTEGER DEFAULT 0,
    last_click_at TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);
```

### clicks Table
```sql
CREATE TABLE clicks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL,
    target TEXT NOT NULL,
    ip TEXT NOT NULL,
    user_agent TEXT NOT NULL,
    referer TEXT,
    created_at TEXT NOT NULL
);
```

## 🔒 Security Features

- **PDO Prepared Statements**: Protection against SQL injection
- **URL Validation**: Only HTTP/HTTPS URLs allowed
- **Session Security**: HttpOnly and SameSite cookies
- **API Authentication**: Bearer token required
- **Password Protection**: Admin panel secured
- **Input Sanitization**: All inputs validated and sanitized
- **Data Directory Protection**: .htaccess prevents direct access

## 🎨 Customization

### Changing Colors
Edit `assets/css/style.css` and modify CSS variables:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --success-color: #10b981;
    --danger-color: #ef4444;
}
```

### Custom Code Length
Edit `includes/functions.php`:
```php
function generateCode($length = 6) {
    // Change default length
}
```

### Session Lifetime
Edit `.env`:
```env
SESSION_LIFETIME=86400  # 24 hours in seconds
```

## 🐛 Troubleshooting

### URLs Not Working
1. Check if mod_rewrite is enabled (Apache)
2. Verify .htaccess is uploaded
3. Check file permissions
4. Ensure BASE_URL is correct in .env

### Database Errors
1. Check data/ directory permissions (755)
2. Ensure SQLite3 extension is enabled
3. Verify PHP has write access to data/

### Telegram Not Working
1. Verify bot token is correct
2. Check chat ID is correct
3. Ensure bot is started (send /start)
4. Check PHP can make external requests

### API Not Working
1. Verify Authorization header is sent
2. Check API key matches .env
3. Ensure Content-Type is application/json
4. Check PHP error logs

## 📊 Performance Tips

1. **Enable OPcache**: Improves PHP performance
2. **Use Redis/APCu**: For caching (optional)
3. **Enable Gzip**: Compress responses
4. **CDN**: Use CDN for static assets
5. **Database Optimization**: Regular VACUUM for SQLite

## 🔄 Backup

### Backup Database
```bash
cp data/shortenv.db data/shortenv.db.backup
```

### Automated Backup
Add to crontab:
```bash
0 2 * * * cp /path/to/data/shortenv.db /path/to/backups/shortenv-$(date +\%Y\%m\%d).db
```

## 📝 License

This project is open source and available under the MIT License.

## 🤝 Support

For issues, questions, or contributions:
1. Check the troubleshooting section
2. Review the code comments
3. Test in a development environment first

## 🎯 Roadmap

- [ ] QR Code generation for short URLs
- [ ] Bulk URL import/export
- [ ] Custom domains support
- [ ] Advanced analytics dashboard
- [ ] Rate limiting
- [ ] URL expiration dates
- [ ] Password-protected URLs
- [ ] Browser extension

## 📸 Screenshots

### Admin Dashboard
Modern, responsive dashboard with statistics and URL management.

### Click Logs
Detailed analytics showing IP, user agent, and timestamp for each click.

### Create Short URL
Simple form to create short URLs with custom codes or auto-generation.

## 🌟 Credits

Built with ❤️ using:
- PHP & SQLite
- Modern CSS (Flexbox/Grid)
- Vanilla JavaScript
- Telegram Bot API

---

**Version**: 1.0.0  
**Last Updated**: 2025-10-08
