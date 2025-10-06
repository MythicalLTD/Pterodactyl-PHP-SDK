# Contributing to Pterodactyl PHP SDK

Thank you for your interest in contributing to the Pterodactyl PHP SDK! This document provides guidelines and information for contributors.

## Code Style

This project follows PSR-12 coding standards. Please ensure your code adheres to these standards before submitting a pull request.

## Development Setup

1. Clone the repository:
```bash
git clone https://github.com/MythicalSystems/Pterodactyl-PHP-SDK.git
cd Pterodactyl-PHP-SDK
```

2. Install dependencies:
```bash
composer install
```

3. Run quality checks:
```bash
composer run quality
```

## Testing

Run the test suite:
```bash
composer test
```

Run tests with coverage:
```bash
composer run test:coverage
```

## Code Quality

The project uses several tools to maintain code quality:

- **PHPUnit** for testing
- **PHPStan** for static analysis
- **PHP_CodeSniffer** for code style checking

Run all quality checks:
```bash
composer run quality
```

## Pull Request Process

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass (`composer test`)
6. Run quality checks (`composer run quality`)
7. Commit your changes (`git commit -m 'Add amazing feature'`)
8. Push to the branch (`git push origin feature/amazing-feature`)
9. Open a Pull Request

## Reporting Issues

When reporting issues, please include:

- PHP version
- SDK version
- Pterodactyl Panel version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Any error messages or logs

## License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.
