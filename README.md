# Pterodactyl PHP SDK

A comprehensive PHP SDK for interacting with the Pterodactyl Panel API, including both Application API and Client API.

[![Latest Version](https://img.shields.io/packagist/v/mythicalsystems/pterodactyl-php-sdk.svg?style=flat-square)](https://packagist.org/packages/mythicalsystems/pterodactyl-php-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/mythicalsystems/pterodactyl-php-sdk.svg?style=flat-square)](https://packagist.org/packages/mythicalsystems/pterodactyl-php-sdk)
[![License](https://img.shields.io/packagist/l/mythicalsystems/pterodactyl-php-sdk.svg?style=flat-square)](https://github.com/MythicalSystems/Pterodactyl-PHP-SDK/blob/main/LICENSE)

## Installation

Install the package via Composer:

```bash
composer require mythicalsystems/pterodactyl-php-sdk
```

## Quick Start

```php
<?php

use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize the SDK with both admin and client API keys
$sdk = new PterodactylSDK(
    'https://your-panel.com',
    'your-admin-api-key',
    'your-client-api-key'
);

// Use the admin API
$servers = $sdk->admin()->servers()->list();

// Use the client API
$account = $sdk->client()->getAccountDetails();
```

## API Clients

### Admin API Only

```php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$admin = PterodactylSDK::adminOnly('https://your-panel.com', 'your-admin-api-key');
```

### Client API Only

```php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$client = PterodactylSDK::clientOnly('https://your-panel.com', 'your-client-api-key');
```


## Features

### Application API

#### Server Management
- Create, update, and delete servers
- List servers with filtering and pagination
- Get detailed server information
- Manage server resources and configurations

#### User Management
- Create, update, and delete users
- List users with filtering and pagination
- Get detailed user information
- Manage user permissions and roles

#### Location Management
- Create, update, and delete locations
- List locations with filtering and pagination
- Get detailed location information

#### Node Management
- Create, update, and delete nodes
- List nodes with filtering and pagination
- Get detailed node information
- Manage node resources and configurations

#### Nest Management
- Create, update, and delete nests
- List nests with filtering and pagination
- Get detailed nest information

#### Egg Management
- Create, update, and delete eggs
- List eggs with filtering and pagination
- Get detailed egg information

### Client API

#### Server Management
- List user's servers
- Get server details
- Get server resources
- Send power signals (start, stop, restart, kill)
- Send console commands

#### File Management
- List files in a directory
- Get file contents
- Write file contents
- Rename files and directories
- Copy files and directories
- Delete files and directories
- Compress files
- Decompress archives

#### Backup Management
- List server backups
- Create new backups
- Get backup details
- Download backups
- Restore backups
- Delete backups

#### Server Transfer
- Start server transfer
- Get transfer status
- Cancel transfer

#### Server Installation
- Get installation status
- Start installation
- Cancel installation

#### WebSocket Support
- Connect to server WebSocket
- Send console commands
- Send power signals
- Subscribe to server stats
- Unsubscribe from server stats

#### Account Management
- Get account details
- Update email address
- Update password
- Enable/disable 2FA
- Manage API keys

#### SSH Key Management
- List SSH keys
- Get key details
- Create new SSH keys
- Delete SSH keys

#### Activity Logs
- Get server activity logs
- Get user activity logs
- Get server audit logs


## Documentation

Comprehensive documentation is available in the [`docs/`](docs/) directory:

- [Installation & Setup](docs/installation.md)
- [Quick Start Guide](docs/quick-start.md)
- [Authentication](docs/authentication.md)
- [Admin API](docs/admin-api.md) - Server, user, node, location, and nest management
- [Client API](docs/client-api.md) - File, database, network, and account management
- [Error Handling](docs/error-handling.md) - Comprehensive error handling guide
- [Examples](docs/examples.md) - Real-world usage examples

## API Reference

### Admin API Resources

- **Servers**: Create, update, delete, and manage servers
- **Users**: Manage user accounts and permissions
- **Locations**: Manage server locations
- **Nodes**: Manage server nodes and resources
- **Nests**: Manage application nests and eggs

### Client API Resources

- **Servers**: List user servers, send commands, manage resources
- **Files**: File operations, directory management, compression
- **Databases**: Database creation, management, and configuration
- **Networks**: Allocation management and network configuration
- **Account**: User account settings and API key management
- **SSH Keys**: SSH key management for server access
- **Activity**: Server and user activity logs
- **Schedules**: Automated task scheduling
- **Startup**: Server startup parameter management

## Error Handling

The SDK provides comprehensive error handling with specific exception types:

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

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.