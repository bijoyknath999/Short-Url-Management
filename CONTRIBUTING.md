# Contributing to Short URL Management System

Thank you for considering contributing to this project! 🎉

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Messages](#commit-messages)
- [Pull Request Process](#pull-request-process)

---

## 🤝 Code of Conduct

- Be respectful and inclusive
- Welcome newcomers
- Focus on constructive feedback
- Help others learn and grow

---

## 💡 How Can I Contribute?

### Reporting Bugs
- Use GitHub Issues
- Include PHP version, server type, and error messages
- Provide steps to reproduce
- Include screenshots if applicable

### Suggesting Features
- Open a GitHub Discussion first
- Explain the use case
- Consider backward compatibility

### Code Contributions
- Fix bugs
- Add new features
- Improve documentation
- Optimize performance
- Enhance security

---

## 🛠️ Development Setup

### Prerequisites
```bash
- PHP 7.4+
- SQLite3
- Apache/Nginx
- Git
```

### Local Setup
```bash
# Clone the repository
git clone https://github.com/yourusername/short-url.git
cd short-url

# Copy environment file
cp .env.example .env

# Set permissions
chmod 755 data
chmod 600 .env

# Start local server (if using PHP built-in)
php -S localhost:8000
```

### Testing
```bash
# Check PHP syntax
find . -name "*.php" -exec php -l {} \;

# Test database connection
php -r "require 'includes/db.php'; Database::getInstance();"
```

---

## 📝 Coding Standards

### PHP
- Follow PSR-12 coding standard
- Use meaningful variable names
- Add comments for complex logic
- Use type hints where possible

```php
// Good
function createShortUrl(string $code, string $target, int $redirectType = 302): bool {
    // Implementation
}

// Avoid
function create($c, $t, $r = 302) {
    // Implementation
}
```

### HTML/CSS
- Use semantic HTML5
- Mobile-first responsive design
- Use CSS variables for theming
- Keep specificity low

### JavaScript
- Use vanilla JavaScript (no jQuery)
- Use modern ES6+ features
- Add comments for complex functions
- Handle errors gracefully

---

## 💬 Commit Messages

### Format
```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `perf`: Performance improvements
- `test`: Adding tests
- `chore`: Maintenance tasks

### Examples
```bash
feat(api): add bulk URL creation endpoint

Add new API endpoint to create multiple short URLs in one request.
Includes validation and error handling.

Closes #123

---

fix(mobile): resolve filter line responsive issue

Filter inputs now stack vertically on mobile devices.
Added !important flags to override inline styles.

Fixes #456

---

docs(readme): update installation instructions

Add Docker installation steps and troubleshooting section.
```

---

## 🔄 Pull Request Process

### Before Submitting
1. **Test your changes**
   - Test on PHP 7.4, 8.0, 8.1, 8.2
   - Test on mobile and desktop
   - Check all affected pages

2. **Update documentation**
   - Update README if needed
   - Add/update code comments
   - Update CHANGELOG.md

3. **Check code quality**
   - Run PHP syntax check
   - Ensure no warnings/errors
   - Follow coding standards

### Submitting PR
1. **Create a feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```

2. **Make your changes**
   ```bash
   git add .
   git commit -m "feat: add amazing feature"
   ```

3. **Push to GitHub**
   ```bash
   git push origin feature/amazing-feature
   ```

4. **Open Pull Request**
   - Use clear title and description
   - Reference related issues
   - Add screenshots if UI changes
   - Wait for review

### PR Template
```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tested on PHP 7.4
- [ ] Tested on PHP 8.0+
- [ ] Tested on mobile
- [ ] Tested on desktop

## Screenshots (if applicable)
Add screenshots here

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-reviewed code
- [ ] Commented complex code
- [ ] Updated documentation
- [ ] No new warnings
```

---

## 🧪 Testing Guidelines

### Manual Testing
- Test all CRUD operations
- Test API endpoints
- Test mobile responsiveness
- Test different browsers
- Test error handling

### Areas to Test
- URL creation (auto and custom)
- URL editing
- URL deletion
- Click tracking
- Telegram notifications
- API authentication
- Date filtering
- Search functionality

---

## 📁 Project Structure

```
short-url/
├── admin/              # Admin panel
├── includes/           # Core PHP files
├── assets/            # CSS, JS, images
├── data/              # Database (gitignored)
├── .github/           # GitHub workflows
└── docs/              # Documentation
```

---

## 🎯 Priority Areas

### High Priority
- Security improvements
- Performance optimization
- Bug fixes
- Mobile responsiveness

### Medium Priority
- New features
- UI improvements
- Documentation
- Code refactoring

### Low Priority
- Code style improvements
- Minor enhancements

---

## 📞 Getting Help

- **Questions**: Open a GitHub Discussion
- **Bugs**: Create an Issue
- **Features**: Start a Discussion first

---

## 🙏 Thank You!

Your contributions make this project better for everyone!

**Happy Coding!** 🚀
