# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 1.0.0

### Added
- Initial release of Pterodactyl PHP SDK
- Support for both Admin and Client APIs
- Comprehensive resource management for servers, users, locations, nodes, and nests
- File management operations
- Backup management
- Server transfer functionality
- WebSocket support
- Account management
- SSH key management
- Activity logging
- Proper exception handling
- PSR-4 autoloading
- Composer package structure

### Changed
- Converted from MythicalDash integration to standalone Composer package
- Removed dependency on MythicalDash\App
- Improved error handling with custom exceptions
- Updated namespace structure for better organization

### Security
- Proper API key handling
- Input validation and sanitization
