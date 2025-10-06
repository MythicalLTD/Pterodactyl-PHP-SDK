# Wings API - Getting Started

This guide will help you get started with the Wings API in the Pterodactyl PHP SDK.

## Prerequisites

- PHP 8.0 or higher
- Access to a Pterodactyl Wings daemon
- Node token ID and secret from your Pterodactyl panel

## Installation

The Wings API is included in the main Pterodactyl PHP SDK package:

```bash
composer require mythicalsystems/pterodactyl-php-sdk
```

## Basic Setup

### Wings-Only Client

```php
<?php
require_once 'vendor/autoload.php';

use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Create Wings-only client
$wings = PterodactylSDK::wingsOnly(
    'wings.example.com',                    // Wings hostname/IP
    8080,                                   // Wings port (default: 8080)
    'https',                                // Protocol (http/https)
    'node-token-id.node-token-secret'       // Wings token
);
```

### Full SDK with Wings

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize full SDK with Wings support
$sdk = new PterodactylSDK(
    'https://your-panel.com',               // Panel URL
    'ptlc_admin_xxxxxxxxxxxxx',            // Admin API Key
    'ptlc_client_xxxxxxxxxxxxx',           // Client API Key
    'wings.example.com',                    // Wings host
    8080,                                   // Wings port
    'https',                                // Wings protocol
    'node-token-id.node-token-secret'       // Wings token
);

// Access Wings API
$wings = $sdk->wings();
```

## Getting Your Wings Token

1. **Log into your Pterodactyl Panel** as an administrator
2. **Navigate to Admin Panel** → **Nodes**
3. **Click on your node** to view details
4. **Copy the Node Token ID** and **Node Token Secret**
5. **Combine them** in the format: `node-token-id.node-token-secret`

Example:
```
Node Token ID: abc123def456
Node Token Secret: xyz789uvw012
Wings Token: abc123def456.xyz789uvw012
```

## Testing Your Connection

```php
<?php
try {
    if ($wings->testConnection()) {
        echo "✅ Successfully connected to Wings!";
    } else {
        echo "❌ Failed to connect to Wings";
    }
} catch (Exception $e) {
    echo "❌ Connection error: " . $e->getMessage();
}
```

## Your First API Call

```php
<?php
try {
    // Get system information
    $system = $wings->getSystem();
    $info = $system->getSystemInfo();
    
    echo "Wings Version: " . $info['version'] . "\n";
    echo "Docker Version: " . $info['docker_version'] . "\n";
    echo "System Time: " . $info['time'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Common Configuration

### Environment Variables

```php
<?php
// Using environment variables
$wings = PterodactylSDK::wingsOnly(
    $_ENV['WINGS_HOST'],
    (int)$_ENV['WINGS_PORT'],
    $_ENV['WINGS_PROTOCOL'],
    $_ENV['WINGS_TOKEN']
);
```

### Configuration File

```php
<?php
// config.php
return [
    'wings' => [
        'host' => 'wings.example.com',
        'port' => 8080,
        'protocol' => 'https',
        'token' => 'node-token-id.node-token-secret'
    ]
];

// Usage
$config = require 'config.php';
$wings = PterodactylSDK::wingsOnly(
    $config['wings']['host'],
    $config['wings']['port'],
    $config['wings']['protocol'],
    $config['wings']['token']
);
```

## Next Steps

Now that you have Wings set up, explore:

- [Authentication](authentication.md) - Understanding Wings authentication
- [System Service](system-service.md) - System information and monitoring
- [Server Service](server-service.md) - Server management operations
- [Docker Service](docker-service.md) - Container and image management
- [Examples](examples.md) - Real-world usage examples
