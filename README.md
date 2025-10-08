# 🔗 Short URL Management System

A powerful, self-hosted URL shortener with click tracking, Telegram integration, and a beautiful admin dashboard.

![Version](https://img.shields.io/badge/version-1.1.0-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/license-MIT-green)
![Mobile](https://img.shields.io/badge/mobile-responsive-brightgreen)

---

## ✨ Features

- 🔗 **URL Shortening** - Create custom or auto-generated short codes
- 📊 **Click Tracking** - Track unique visitors with IP, user agent, and referer
- 📱 **Telegram Integration** - Real-time notifications for every click
- 🎯 **Date Filtering** - Filter URLs by creation date (default: today)
- 📈 **Analytics Dashboard** - Beautiful stats and click logs
- 🔐 **Secure API** - RESTful API with Bearer token authentication
- 📱 **Mobile Responsive** - Works perfectly on all devices
- ⚡ **Zero Dependencies** - Pure PHP, no external packages needed
- 🗄️ **SQLite Database** - Lightweight and portable
- 🎨 **Modern UI** - Clean gradient design with smooth animations

---

## 🚀 Quick Start

### Requirements
- PHP 7.4 or higher
- SQLite3 extension
- Apache/Nginx with mod_rewrite

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/short-url.git
cd short-url
```

2. **Configure environment**
```bash
cp .env.example .env
nano .env  # Edit your settings
```

3. **Set permissions**
```bash
chmod 755 data
chmod 600 .env
```

4. **Access admin panel**
```
https://yourdomain.com/admin/login.php
Default password: @4321bkna (change this!)
```

---

## 📖 Documentation

- **[Installation Guide](INSTALLATION.md)** - Detailed setup instructions
- **[API Documentation](API_DOCUMENTATION.md)** - Complete API reference
- **[Security Guide](SECURITY.md)** - Security best practices
- **[cPanel Deployment](CPANEL_DEPLOYMENT_GUIDE.md)** - Deploy to shared hosting

---

## 🎯 Key Features

### Unique Click Tracking
- Tracks only **one click per IP** per URL
- No duplicate counting
- Accurate visitor statistics

### Date Filtering
- Filter dashboard by date
- **Default: Today's URLs**
- Easy date picker interface

### Telegram Integration
- Real-time click notifications
- Full user agent display
- Easy web-based configuration

### Mobile Responsive
- Perfect on all screen sizes
- Touch-friendly interface
- Optimized forms and buttons

---

## 🔧 Configuration

Edit `.env` file:

```env
# Admin Configuration
ADMIN_PASSWORD=your_secure_password
ADMIN_KEY=your_api_key_here

# Telegram (Optional)
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id

# Base URL
BASE_URL=https://yourdomain.com

# Database
DB_PATH=data/shortenv.db
```

---

## 📊 API Usage

### Create Short URL
```bash
curl -X POST "https://yourdomain.com/api.php?action=create" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "target": "https://example.com",
    "code": "custom-code",
    "redirect_type": 302
  }'
```

### List All URLs
```bash
curl "https://yourdomain.com/api.php?action=list" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for complete reference.

---

## 🎨 Screenshots

### Admin Dashboard
- Beautiful gradient design
- Real-time statistics
- Date filtering
- Search functionality

### Click Logs
- Detailed analytics
- Full user agent display
- IP tracking
- Referer information

### Mobile View
- Fully responsive
- Touch-friendly
- Optimized layouts

---

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+
- **Database**: SQLite3
- **Frontend**: Vanilla JavaScript
- **Styling**: Custom CSS with CSS Variables
- **Server**: Apache/Nginx

---

## 📦 What's Included

```
short-url/
├── index.php              # Main redirect handler
├── api.php                # RESTful API
├── admin/                 # Admin dashboard
│   ├── dashboard.php      # Main dashboard
│   ├── create.php         # Create URLs
│   ├── edit.php           # Edit URLs
│   ├── click_logs.php     # Analytics
│   └── settings.php       # Telegram config
├── includes/              # Core system
│   ├── config.php         # Configuration
│   ├── db.php             # Database
│   └── functions.php      # Utilities
├── assets/                # Frontend
│   ├── css/style.css      # Styles
│   └── js/script.js       # JavaScript
└── data/                  # Database directory
```

---

## 🔐 Security Features

- ✅ PDO prepared statements (SQL injection prevention)
- ✅ Input validation and sanitization
- ✅ XSS prevention
- ✅ Session security (HttpOnly, SameSite)
- ✅ API Bearer token authentication
- ✅ Protected .env files
- ✅ Protected data directory

---

## 🚀 Deployment

### cPanel Hosting
See [CPANEL_DEPLOYMENT_GUIDE.md](CPANEL_DEPLOYMENT_GUIDE.md)

### VPS/Dedicated Server
1. Upload files to web root
2. Configure .env
3. Set permissions
4. Configure web server
5. Enable SSL

### Docker (Coming Soon)
Docker support will be added in future releases.

---

## 📝 Changelog

### Version 1.1.0 (2025-10-08)
- ✅ Added unique click tracking (one per IP)
- ✅ Added date filter with today as default
- ✅ Full user agent display in Telegram
- ✅ Telegram settings page
- ✅ Auto-detecting BASE_URL
- ✅ Mobile responsive improvements
- ✅ Copy-friendly Telegram messages

### Version 1.0.0
- Initial release
- Basic URL shortening
- Click tracking
- Admin dashboard
- API endpoints

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 🙏 Acknowledgments

- Built with ❤️ using pure PHP
- Inspired by modern URL shorteners
- Designed for simplicity and performance

---

## 📞 Support

- **Documentation**: Check the `/docs` folder
- **Issues**: [GitHub Issues](https://github.com/yourusername/short-url/issues)
- **Discussions**: [GitHub Discussions](https://github.com/yourusername/short-url/discussions)

---

## ⭐ Star History

If you find this project useful, please consider giving it a star!

---

**Made with ❤️ by [Your Name]**

**Version**: 1.1.0  
**Last Updated**: 2025-10-08  
**Status**: Production Ready ✅
