# Wings API Documentation

Welcome to the comprehensive Wings API documentation. Wings is the daemon that runs on each node and manages the actual game servers.

## Table of Contents

- [Getting Started](getting-started.md)
- [Authentication](authentication.md)
- [System Service](system-service.md)
- [Server Service](server-service.md)
- [Docker Service](docker-service.md)
- [Transfer Service](transfer-service.md)
- [JWT Service](jwt-service.md)
- [Error Handling](error-handling.md)
- [Examples](examples.md)

## What is Wings?

Wings is the daemon that runs on each node in your Pterodactyl installation. It's responsible for:

- **Server Management** - Creating, starting, stopping, and managing game servers
- **Resource Monitoring** - Tracking CPU, memory, and disk usage
- **Docker Integration** - Managing containers and images
- **File Operations** - Handling server files and directories
- **Network Management** - Managing server ports and connections
- **Transfer Operations** - Moving servers between nodes

## Quick Start

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize Wings client
$wings = PterodactylSDK::wingsOnly(
    'wings.example.com',                    // Wings host
    8080,                                   // Wings port
    'https',                                // Protocol
    'node-token-id.node-token-secret'       // Wings token
);

// Test connection
if ($wings->testConnection()) {
    echo "Connected to Wings successfully!";
}
```

## Services Overview

### System Service
- Get system information and statistics
- Monitor resource usage
- Check Wings and Docker versions

### Server Service
- Create, delete, and manage servers
- Send power commands (start, stop, restart, kill)
- Access server logs and console
- Monitor server resources

### Docker Service
- List and manage containers
- Pull and manage Docker images
- Container operations and monitoring

### Transfer Service
- Transfer servers between nodes
- Monitor transfer progress
- Cancel transfers

### JWT Service
- Generate secure tokens for server access
- File access tokens
- Authentication tokens

## Getting Help

- [GitHub Issues](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/issues)
- [Documentation](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/tree/main/docs)
- [Contributing](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/blob/main/CONTRIBUTING.md)
