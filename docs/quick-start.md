# Quick Start Guide

This guide will get you up and running with the Pterodactyl PHP SDK in just a few minutes.

## Basic Setup

```php
<?php
require_once 'vendor/autoload.php';

use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize the SDK
$sdk = new PterodactylSDK(
    'https://your-panel.com',
    'your-admin-api-key',
    'your-client-api-key'
);
```

## Your First API Call

### List All Servers (Admin)

```php
<?php
try {
    $servers = $sdk->admin()->servers()->listServers();
    
    foreach ($servers['data'] as $server) {
        echo "Server: " . $server['attributes']['name'] . "\n";
        echo "Status: " . $server['attributes']['status'] . "\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Get Account Details (Client)

```php
<?php
try {
    $account = $sdk->client()->getAccountDetails();
    
    echo "Email: " . $account['attributes']['email'] . "\n";
    echo "Username: " . $account['attributes']['username'] . "\n";
    echo "First Name: " . $account['attributes']['first_name'] . "\n";
    echo "Last Name: " . $account['attributes']['last_name'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Common Operations

### Get Server Details

```php
<?php
// Admin: Get server by ID
$server = $sdk->admin()->servers()->getServer(1);

// Client: Get user's server by identifier
$server = $sdk->client()->servers()->getServer('abc123def');
```

### List User's Servers

```php
<?php
$servers = $sdk->client()->servers()->listServers();

foreach ($servers['data'] as $server) {
    echo "Server: " . $server['attributes']['name'] . "\n";
    echo "Identifier: " . $server['attributes']['identifier'] . "\n";
    echo "Status: " . $server['attributes']['status'] . "\n";
    echo "---\n";
}
```

### Send Power Command

```php
<?php
try {
    // Start a server
    $sdk->client()->servers()->sendPowerCommand('abc123def', 'start');
    echo "Server start command sent!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### List Files in Server Directory

```php
<?php
try {
    $files = $sdk->client()->files()->listFiles('abc123def', '/');
    
    foreach ($files['data'] as $file) {
        echo "File: " . $file['attributes']['name'] . "\n";
        echo "Type: " . $file['attributes']['mimetype'] . "\n";
        echo "Size: " . $file['attributes']['size'] . " bytes\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Error Handling

The SDK provides comprehensive error handling:

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PermissionException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ResourceNotFoundException;

try {
    $server = $sdk->admin()->servers()->getServer(999);
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (PermissionException $e) {
    echo "Permission denied: " . $e->getMessage();
} catch (ResourceNotFoundException $e) {
    echo "Server not found: " . $e->getMessage();
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage();
}
```

## Using Only One API

### Admin API Only

```php
<?php
$admin = PterodactylSDK::adminOnly(
    'https://your-panel.com',
    'your-admin-api-key'
);

$servers = $admin->servers()->listServers();
```

### Client API Only

```php
<?php
$client = PterodactylSDK::clientOnly(
    'https://your-panel.com',
    'your-client-api-key'
);

$account = $client->getAccountDetails();
```

## Next Steps

Now that you have the basics working, explore:

- [Admin API Documentation](admin-api.md) - For administrative operations
- [Client API Documentation](client-api.md) - For user operations
- [Examples](examples.md) - More detailed examples
- [Error Handling](error-handling.md) - Comprehensive error handling guide
