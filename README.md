# 🦕 Pterodactyl PHP SDK

> **The most comprehensive PHP SDK for Pterodactyl Panel API**  
> Complete replacement for abandoned/outdated Pterodactyl SDKs with full **Admin**, **Client** & **Wings** API support

[![Latest Version](https://img.shields.io/packagist/v/mythicalsystems/pterodactyl-php-sdk.svg?style=flat-square)](https://packagist.org/packages/mythicalsystems/pterodactyl-php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/mythicalsystems/pterodactyl-php-sdk.svg?style=flat-square)](https://packagist.org/packages/mythicalsystems/pterodactyl-php-sdk)
[![License](https://img.shields.io/packagist/l/mythicalsystems/pterodactyl-php-sdk.svg?style=flat-square)](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/blob/main/LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/mythicalsystems/pterodactyl-php-sdk.svg?style=flat-square)](https://php.net)
[![Build Status](https://img.shields.io/github/actions/workflow/status/mythicalltd/Pterodactyl-PHP-SDK/ci.yml?style=flat-square)](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/actions)

## 🚀 Quick Start

```bash
composer require mythicalsystems/pterodactyl-php-sdk
```

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize the SDK with all APIs
$sdk = new PterodactylSDK(
    'https://your-panel.com',           // Panel URL
    'ptlc_admin_xxxxxxxxxxxxx',         // Admin API Key
    'ptlc_client_xxxxxxxxxxxxx',        // Client API Key
    'wings.example.com',                // Wings Host (optional)
    8080,                               // Wings Port
    'https',                            // Wings Protocol
    'node-token-id.node-token-secret'   // Wings Token (node-token-id.node-token-secret)
);

// Use any API
$servers = $sdk->admin()->servers()->listServers();
$account = $sdk->client()->getAccountDetails();
$system = $sdk->wings()->getSystem();
```

## 📚 Documentation

**👉 [View Complete Documentation](docs/)**

### 🎛️ Panel API
- **[Panel Overview](docs/panel/README.md)** - Complete Panel API documentation
- **[Getting Started](docs/panel/getting-started.md)** - Setup and basic usage
- **[Admin API](docs/panel/admin-api.md)** - Administrative operations
- **[Client API](docs/panel/client-api.md)** - User operations

### 🚀 Wings API
- **[Wings Overview](docs/wings/README.md)** - Complete Wings API documentation
- **[Getting Started](docs/wings/getting-started.md)** - Setup and basic usage
- **[System Service](docs/wings/system-service.md)** - System monitoring
- **[Server Service](docs/wings/server-service.md)** - Server management
- **[Docker Service](docs/wings/docker-service.md)** - Container management
- **[Transfer Service](docs/wings/transfer-service.md)** - Server transfers
- **[JWT Service](docs/wings/jwt-service.md)** - Token generation

### 📖 General
- **[Installation](docs/installation.md)** - Package installation
- **[Authentication](docs/authentication.md)** - API key setup
- **[Error Handling](docs/error-handling.md)** - Exception management
- **[Examples](docs/examples.md)** - Real-world scenarios


## ✨ Features

### 🎛️ **Admin API** - Panel Administration
- **Server Management** - Create, update, delete, and manage servers
- **User Management** - User accounts, permissions, and roles
- **Node Management** - Server nodes and resource allocation
- **Location Management** - Geographic server locations
- **Nest & Egg Management** - Application templates and configurations

### 👤 **Client API** - User Operations
- **Server Control** - Power commands, console access, resource monitoring
- **File Management** - Upload, download, edit, compress files
- **Database Management** - Create, manage, and configure databases
- **Backup System** - Automated backups and restoration
- **Account Settings** - Profile management and API keys
- **SSH Keys** - Secure server access management

### 🚀 **Wings API** - Direct Daemon Access
- **System Monitoring** - Real-time stats and resource usage
- **Server Operations** - Direct server control and management
- **Docker Integration** - Container and image management
- **Transfer System** - Server migration between nodes
- **JWT Tokens** - Secure authentication tokens


## 🔧 API Clients

### Individual API Access

```php
// Admin API Only
$admin = PterodactylSDK::adminOnly('https://panel.com', 'admin-key');

// Client API Only  
$client = PterodactylSDK::clientOnly('https://panel.com', 'client-key');

// Wings API Only
$wings = PterodactylSDK::wingsOnly('wings.com', 8080, 'https', 'node-id.node-secret');
```

## 🛡️ Error Handling

```php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PermissionException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ResourceNotFoundException;

try {
    $server = $sdk->admin()->servers()->getServer(1);
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (PermissionException $e) {
    echo "Permission denied: " . $e->getMessage();
} catch (ResourceNotFoundException $e) {
    echo "Server not found: " . $e->getMessage();
}
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

<div align="center">

**⭐ Star this repo if you find it useful!**

[📖 Documentation](docs/) • [🐛 Issues](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/issues) • [💬 Discussions](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/discussions)

</div>