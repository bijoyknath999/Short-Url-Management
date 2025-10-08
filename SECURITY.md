# 🔒 Security Guidelines

## Security Features

### 1. Authentication & Authorization
- **Password Protection**: Admin panel requires password authentication
- **Session Security**: HttpOnly and SameSite cookies prevent XSS attacks
- **API Authentication**: Bearer token required for all API requests
- **Session Timeout**: Configurable session lifetime

### 2. Input Validation
- **URL Validation**: Only HTTP/HTTPS URLs accepted
- **Code Validation**: Alphanumeric, hyphens, underscores only (3-50 chars)
- **SQL Injection Prevention**: PDO prepared statements for all queries
- **XSS Prevention**: All output escaped with htmlspecialchars()

### 3. Database Security
- **SQLite**: Lightweight, file-based database
- **Prepared Statements**: All queries use parameterized statements
- **Directory Protection**: .htaccess prevents direct access to data/
- **No Raw Queries**: All database operations use safe functions

### 4. File Security
- **.env Protection**: .htaccess blocks access to .env files
- **Data Directory**: Protected from direct web access
- **File Permissions**: Proper permissions (755 for dirs, 644 for files)

### 5. API Security
- **Bearer Token**: Required for all API endpoints
- **HTTPS Recommended**: Use SSL/TLS for production
- **Rate Limiting**: Consider implementing for production
- **CORS**: Configure as needed for your use case

## Security Best Practices

### 1. Change Default Password
```env
ADMIN_PASSWORD=your_very_secure_password_here
```

### 2. Generate Strong API Key
```bash
# Generate a secure random key
openssl rand -hex 32
```

### 3. Use HTTPS
Always use HTTPS in production:
```env
BASE_URL=https://yourdomain.com
```

### 4. Secure .env File
```bash
chmod 600 .env
```

### 5. Regular Backups
```bash
# Daily backup cron job
0 2 * * * cp /path/to/data/shortenv.db /backups/shortenv-$(date +\%Y\%m\%d).db
```

### 6. Update PHP
Keep PHP updated to latest stable version for security patches.

### 7. Disable Directory Listing
Add to .htaccess:
```apache
Options -Indexes
```

### 8. Implement Rate Limiting
For production, add rate limiting to prevent abuse:
```php
// Example using Redis
$redis = new Redis();
$key = "rate_limit:" . $ip;
$requests = $redis->incr($key);
if ($requests == 1) {
    $redis->expire($key, 60);
}
if ($requests > 100) {
    http_response_code(429);
    die('Rate limit exceeded');
}
```

### 9. Monitor Logs
Regularly check server logs for suspicious activity:
```bash
tail -f /var/log/apache2/error.log
```

### 10. Restrict Admin Access
Consider IP whitelisting for admin panel:
```apache
<Directory "/path/to/admin">
    Order deny,allow
    Deny from all
    Allow from 192.168.1.0/24
</Directory>
```

## Vulnerability Reporting

If you discover a security vulnerability:
1. **Do not** create a public issue
2. Document the vulnerability details
3. Include steps to reproduce
4. Suggest a fix if possible

## Security Checklist

Before deploying to production:

- [ ] Changed default admin password
- [ ] Generated strong API key
- [ ] Configured HTTPS
- [ ] Set proper file permissions
- [ ] Protected .env file
- [ ] Enabled error logging
- [ ] Disabled display_errors in PHP
- [ ] Configured backups
- [ ] Tested all security features
- [ ] Reviewed server configuration
- [ ] Implemented rate limiting (optional)
- [ ] Set up monitoring/alerts

## Common Security Mistakes

### ❌ Don't Do This
```php
// Direct SQL query (vulnerable to SQL injection)
$result = $db->query("SELECT * FROM urls WHERE code = '$code'");

// Unescaped output (vulnerable to XSS)
echo $userInput;

// Hardcoded credentials
$password = "admin123";
```

### ✅ Do This Instead
```php
// Prepared statement
$stmt = $db->prepare("SELECT * FROM urls WHERE code = ?");
$stmt->execute([$code]);

// Escaped output
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// Environment variables
$password = getenv('ADMIN_PASSWORD');
```

## Security Headers

Add these headers to your web server config:

### Apache (.htaccess)
```apache
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Content-Security-Policy "default-src 'self'"
```

### Nginx
```nginx
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

## Database Security

### Backup Encryption
```bash
# Encrypt backup
openssl enc -aes-256-cbc -salt -in shortenv.db -out shortenv.db.enc

# Decrypt backup
openssl enc -d -aes-256-cbc -in shortenv.db.enc -out shortenv.db
```

### Regular Maintenance
```bash
# Optimize SQLite database
sqlite3 data/shortenv.db "VACUUM;"
```

## Telegram Security

- **Keep Bot Token Secret**: Never expose in public repositories
- **Verify Chat ID**: Ensure notifications go to correct chat
- **Use Private Chats**: Don't send sensitive data to public groups

## Monitoring

### Log Suspicious Activity
Monitor for:
- Multiple failed login attempts
- Unusual API usage patterns
- Large number of requests from single IP
- Attempts to access protected files
- SQL injection attempts in logs

### Set Up Alerts
```bash
# Example: Alert on failed login attempts
grep "Invalid password" /var/log/apache2/access.log | mail -s "Failed Login Alert" admin@example.com
```

## Incident Response

If you suspect a security breach:

1. **Immediate Actions**:
   - Change admin password
   - Regenerate API key
   - Review access logs
   - Check for unauthorized URLs

2. **Investigation**:
   - Review click logs for suspicious activity
   - Check database for unauthorized changes
   - Analyze server logs

3. **Recovery**:
   - Restore from clean backup if needed
   - Update all credentials
   - Patch vulnerabilities
   - Document incident

## Compliance

### GDPR Considerations
- IP addresses are personal data
- Implement data retention policy
- Provide data export functionality
- Allow users to request data deletion

### Data Retention
```php
// Example: Delete old click logs (90 days)
$db->exec("DELETE FROM clicks WHERE created_at < datetime('now', '-90 days')");
```

## Security Updates

Stay informed about:
- PHP security updates
- SQLite vulnerabilities
- Web server security patches
- Third-party library updates

## Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [SQLite Security](https://www.sqlite.org/security.html)

---

**Last Updated**: 2025-10-08  
**Security Version**: 1.0
