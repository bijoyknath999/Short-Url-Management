# 🚀 cPanel Production Deployment Guide

Complete guide to deploy your Short URL system on cPanel hosting.

---

## 📋 Pre-Deployment Checklist

- [ ] cPanel hosting account with PHP 7.4+
- [ ] Domain or subdomain configured
- [ ] FTP/File Manager access
- [ ] SSL certificate (recommended)
- [ ] Telegram Bot Token (optional)
- [ ] Telegram Chat ID (optional)

---

## 🎯 Step-by-Step Deployment

### **Step 1: Prepare Files for Upload**

**Option A: Upload entire folder**
- Zip the `Short-Url` folder
- Upload via cPanel File Manager

**Option B: FTP Upload**
- Use FileZilla or any FTP client
- Upload all files to your domain folder

---

### **Step 2: Choose Installation Location**

#### **Option 1: Main Domain (Recommended)**
Upload files to: `/public_html/`

Your URLs will be:
```
https://yourdomain.com/admin/login.php
https://yourdomain.com/abc123
```

#### **Option 2: Subdomain**
Create subdomain: `short.yourdomain.com`
Upload files to: `/public_html/short/`

Your URLs will be:
```
https://short.yourdomain.com/admin/login.php
https://short.yourdomain.com/abc123
```

#### **Option 3: Subfolder**
Upload files to: `/public_html/short-url/`

Your URLs will be:
```
https://yourdomain.com/short-url/admin/login.php
https://yourdomain.com/short-url/abc123
```

---

### **Step 3: Upload Files via cPanel**

1. **Login to cPanel**
2. **Go to File Manager**
3. **Navigate to your target directory** (e.g., `public_html`)
4. **Click "Upload"**
5. **Select all files** or upload zip file
6. **If zip: Right-click → Extract**
7. **Delete the zip file after extraction**

---

### **Step 4: Set File Permissions**

**Important permissions:**

```
Folders: 755
Files: 644
.env file: 600 (after configuration)
data/ folder: 755 or 777
```

**In cPanel File Manager:**
1. Select `data` folder
2. Click "Permissions"
3. Set to `755` or `777` (if 755 doesn't work)
4. Check "Recurse into subdirectories"
5. Click "Change Permissions"

**For .env file (after creating it):**
1. Select `.env` file
2. Click "Permissions"
3. Set to `600` (Owner: Read+Write only)

---

### **Step 5: Configure .env File**

1. **In File Manager, navigate to your installation folder**
2. **Find `.env.example` file**
3. **Right-click → Copy**
4. **Rename copy to `.env`**
5. **Right-click `.env` → Edit**
6. **Update these values:**

```env
# Admin Configuration
ADMIN_PASSWORD=your_secure_password_here
ADMIN_KEY=your_generated_api_key_here

# Telegram Configuration (optional)
TELEGRAM_BOT_TOKEN=your_bot_token_here
TELEGRAM_CHAT_ID=your_chat_id_here

# Base URL - IMPORTANT: Update this!
BASE_URL=https://yourdomain.com

# For subfolder installation:
# BASE_URL=https://yourdomain.com/short-url

# For subdomain:
# BASE_URL=https://short.yourdomain.com

# Database Path (keep as is)
DB_PATH=data/shortenv.db

# Session Configuration
SESSION_LIFETIME=86400
```

7. **Click "Save Changes"**
8. **Set permissions to 600**

---

### **Step 6: Update .htaccess for Your Installation**

**If installed in MAIN DOMAIN (`/public_html/`):**

`.htaccess` should have:
```apache
RewriteEngine On
RewriteBase /

# Redirect /s/code to index.php
RewriteRule ^s/([a-zA-Z0-9_-]+)$ index.php?code=$1 [L,QSA]

# Redirect /code to index.php
RewriteRule ^([a-zA-Z0-9_-]+)$ index.php?code=$1 [L,QSA]

# Prevent direct access to data directory
RewriteRule ^data/ - [F,L]

# Prevent access to .env files
<Files ".env*">
    Order allow,deny
    Deny from all
</Files>
```

**If installed in SUBFOLDER (`/public_html/short-url/`):**

`.htaccess` should have:
```apache
RewriteEngine On
RewriteBase /short-url/

# Redirect /s/code to index.php
RewriteRule ^s/([a-zA-Z0-9_-]+)$ index.php?code=$1 [L,QSA]

# Redirect /code to index.php
RewriteRule ^([a-zA-Z0-9_-]+)$ index.php?code=$1 [L,QSA]

# Prevent direct access to data directory
RewriteRule ^data/ - [F,L]

# Prevent access to .env files
<Files ".env*">
    Order allow,deny
    Deny from all
</Files>
```

**If installed in SUBDOMAIN (`short.yourdomain.com`):**

`.htaccess` should have:
```apache
RewriteEngine On
RewriteBase /

# Redirect /s/code to index.php
RewriteRule ^s/([a-zA-Z0-9_-]+)$ index.php?code=$1 [L,QSA]

# Redirect /code to index.php
RewriteRule ^([a-zA-Z0-9_-]+)$ index.php?code=$1 [L,QSA]

# Prevent direct access to data directory
RewriteRule ^data/ - [F,L]

# Prevent access to .env files
<Files ".env*">
    Order allow,deny
    Deny from all
</Files>
```

---

### **Step 7: Verify PHP Version**

1. **In cPanel, go to "Select PHP Version"**
2. **Select PHP 7.4 or higher** (8.0+ recommended)
3. **Enable these extensions:**
   - ✅ pdo
   - ✅ pdo_sqlite
   - ✅ sqlite3
   - ✅ json
   - ✅ curl
4. **Click "Save"**

---

### **Step 8: Test Your Installation**

1. **Visit your admin login:**
   ```
   https://yourdomain.com/admin/login.php
   ```

2. **Login with your password** (from .env file)

3. **Create a test short URL**

4. **Test the redirect**

5. **Check click logs**

---

### **Step 9: Setup SSL (HTTPS)**

**In cPanel:**
1. Go to "SSL/TLS Status"
2. Click "Run AutoSSL" (if available)
3. Or install Let's Encrypt certificate
4. Wait for SSL to activate

**Update .env after SSL:**
```env
BASE_URL=https://yourdomain.com
```

---

### **Step 10: Configure Telegram (Optional)**

1. **Login to admin panel**
2. **Go to "⚙️ Settings"**
3. **Enter Bot Token and Chat ID**
4. **Click Save**
5. **Check Telegram for test message**

---

## 🔒 Security Hardening for Production

### **1. Change Default Password**
```env
ADMIN_PASSWORD=use_a_very_strong_password_here
```

### **2. Generate Secure API Key**
Use a password generator or run this in terminal:
```bash
openssl rand -hex 32
```

Then add to .env:
```env
ADMIN_KEY=your_generated_64_character_key_here
```

### **3. Protect .env File**
In File Manager:
- Select `.env`
- Permissions → `600`
- Only owner can read/write

### **4. Verify .htaccess Protection**
Try accessing:
```
https://yourdomain.com/.env
https://yourdomain.com/data/
```
Both should show **403 Forbidden** ✅

### **5. Disable Directory Listing**
Add to `.htaccess`:
```apache
Options -Indexes
```

### **6. Add Security Headers**
Add to `.htaccess`:
```apache
# Security Headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

---

## 📊 Post-Deployment Checklist

- [ ] Admin login works
- [ ] Can create short URLs
- [ ] Short URLs redirect correctly
- [ ] Click tracking works
- [ ] Full user agent displayed
- [ ] Telegram notifications working (if configured)
- [ ] SSL certificate active (HTTPS)
- [ ] .env file protected (403 error)
- [ ] data/ directory protected (403 error)
- [ ] Changed default password
- [ ] Generated secure API key
- [ ] Tested on mobile devices
- [ ] Backup database configured

---

## 🔄 Regular Maintenance

### **Daily**
- Check error logs in cPanel
- Monitor unusual activity

### **Weekly**
- Backup database (`data/shortenv.db`)
- Review click analytics
- Check disk space

### **Monthly**
- Update PHP version if needed
- Review security logs
- Clean old click logs (optional)
- Test backup restoration

---

## 💾 Backup Instructions

### **Manual Backup**
1. Go to cPanel File Manager
2. Navigate to `data/` folder
3. Right-click `shortenv.db`
4. Download to your computer
5. Store safely with date: `shortenv-2025-10-08.db`

### **Automated Backup (cPanel)**
1. Go to "Backup" in cPanel
2. Enable "Automatic Backups"
3. Set schedule (daily/weekly)
4. Configure backup destination

### **Download Full Backup**
1. Go to cPanel "Backup"
2. Click "Download a Full Account Backup"
3. Wait for email notification
4. Download backup file

---

## 🐛 Troubleshooting

### **Issue: 500 Internal Server Error**
**Solution:**
- Check `.htaccess` syntax
- Verify PHP version (7.4+)
- Check file permissions (755/644)
- Review error logs in cPanel

### **Issue: Database Error**
**Solution:**
```bash
# In File Manager, set data/ permissions to 777
# Then try again
```

### **Issue: Short URLs return 404**
**Solution:**
- Verify `.htaccess` exists
- Check `RewriteBase` matches your folder
- Ensure mod_rewrite is enabled (usually is on cPanel)

### **Issue: Can't save Telegram settings**
**Solution:**
```bash
# Set .env file permissions to 666
# After saving, change back to 600
```

### **Issue: Redirects to wrong URL**
**Solution:**
- Check `BASE_URL` in `.env`
- Must match your actual domain
- No trailing slash

---

## 📱 Mobile Testing

Test on:
- [ ] iPhone Safari
- [ ] Android Chrome
- [ ] iPad
- [ ] Desktop Chrome
- [ ] Desktop Firefox
- [ ] Desktop Safari

---

## 🎯 Performance Optimization

### **Enable OPcache** (if available)
In cPanel → Select PHP Version → Enable OPcache

### **Enable Gzip Compression**
Add to `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### **Browser Caching**
Add to `.htaccess`:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
</IfModule>
```

---

## 📞 Support Resources

### **cPanel Documentation**
- [cPanel User Guide](https://docs.cpanel.net/)
- [PHP Selector](https://docs.cpanel.net/cpanel/software/select-php-version/)

### **Your System Documentation**
- `README.md` - Complete user guide
- `SECURITY.md` - Security best practices
- `API_DOCUMENTATION.md` - API reference

---

## ✅ Production Deployment Complete!

Your Short URL system is now:
- ✅ Deployed on cPanel
- ✅ SSL secured (HTTPS)
- ✅ Production-ready
- ✅ Backed up
- ✅ Monitored

**Start creating short URLs for your production traffic!** 🎉

---

**Deployment Guide Version**: 1.0  
**Last Updated**: 2025-10-08  
**Status**: Production Ready ✅
