# Contributing to Brayne Jewelry Manager

Thank you for your interest in contributing to Brayne Jewelry Manager! This document provides guidelines and information for contributors.

## 🤝 How to Contribute

### Reporting Bugs
- Use the GitHub issue tracker
- Include detailed steps to reproduce the bug
- Provide system information (OS, PHP version, Laravel version)
- Include error messages and screenshots if applicable

### Suggesting Features
- Use the GitHub issue tracker with the "enhancement" label
- Describe the feature and its benefits
- Include mockups or examples if possible

### Code Contributions
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests if applicable
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## 📋 Development Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite
- Git

### Local Development
1. Fork and clone the repository
2. Install dependencies: `composer install && npm install`
3. Copy `.env.example` to `.env` and configure
4. Run migrations: `php artisan migrate:fresh --seed`
5. Create storage link: `php artisan storage:link`
6. Build assets: `npm run build`
7. Start server: `php artisan serve`

## 🧪 Testing

### Running Tests
```bash
php artisan test
```

### Code Style
We use Laravel Pint for code styling:
```bash
./vendor/bin/pint
```

## 📝 Code Standards

### PHP
- Follow PSR-12 coding standards
- Use type hints where possible
- Add PHPDoc comments for public methods
- Keep methods small and focused

### JavaScript
- Use ES6+ features
- Follow consistent naming conventions
- Add JSDoc comments for functions

### Database
- Use meaningful migration names
- Add indexes for frequently queried columns
- Use foreign key constraints
- Follow Laravel naming conventions

## 🏗️ Architecture Guidelines

### Controllers
- Keep controllers thin
- Use form requests for validation
- Return consistent JSON responses
- Handle exceptions gracefully

### Models
- Use relationships effectively
- Add accessors/mutators when needed
- Use scopes for common queries
- Implement proper attribute casting

### Views
- Use Blade components for reusable UI
- Keep views simple and readable
- Use proper semantic HTML
- Ensure accessibility compliance

## 🔒 Security

### General Guidelines
- Never commit sensitive data (passwords, API keys)
- Validate all user inputs
- Use CSRF protection
- Implement proper authentication
- Follow OWASP security guidelines

### Database Security
- Use prepared statements (Laravel does this automatically)
- Validate and sanitize all inputs
- Use proper access controls
- Implement audit logging for sensitive operations

## 📚 Documentation

### Code Documentation
- Document complex business logic
- Add inline comments for non-obvious code
- Keep README.md updated
- Document API endpoints

### User Documentation
- Update user guides when features change
- Include screenshots for UI changes
- Provide clear installation instructions

## 🚀 Release Process

### Versioning
We use [Semantic Versioning](https://semver.org/):
- MAJOR.MINOR.PATCH
- MAJOR: Breaking changes
- MINOR: New features (backward compatible)
- PATCH: Bug fixes (backward compatible)

### Release Checklist
- [ ] All tests pass
- [ ] Documentation is updated
- [ ] Changelog is updated
- [ ] Version number is incremented
- [ ] Release notes are written

## 🤝 Community Guidelines

### Communication
- Be respectful and inclusive
- Use clear and constructive language
- Provide helpful feedback
- Ask questions when needed

### Code Review
- Review code thoroughly
- Provide constructive feedback
- Test changes locally
- Ensure security best practices

## 📞 Getting Help

- Create an issue on GitHub
- Check existing issues and discussions
- Review documentation
- Join our community discussions

## 🙏 Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes
- Project documentation

Thank you for contributing to Brayne Jewelry Manager! 🏪 