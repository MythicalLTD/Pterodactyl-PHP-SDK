# Panel API - Getting Started

This guide will help you get started with the Panel API in the Pterodactyl PHP SDK.

## Prerequisites

- PHP 8.0 or higher
- Access to a Pterodactyl Panel installation
- Admin API key (for admin operations)
- Client API key (for client operations)

## Installation

The Panel API is included in the main Pterodactyl PHP SDK package:

```bash
composer require mythicalsystems/pterodactyl-php-sdk
```

## Basic Setup

### Full SDK with Both APIs

```php
<?php
require_once 'vendor/autoload.php';

use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize full SDK with both Admin and Client APIs
$sdk = new PterodactylSDK(
    'https://your-panel.com',               // Panel URL
    'ptlc_admin_xxxxxxxxxxxxx',            // Admin API Key
    'ptlc_client_xxxxxxxxxxxxx'            // Client API Key
);
```

### Admin API Only

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Create Admin-only client
$admin = PterodactylSDK::adminOnly(
    'https://your-panel.com',
    'ptlc_admin_xxxxxxxxxxxxx'
);
```

### Client API Only

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Create Client-only client
$client = PterodactylSDK::clientOnly(
    'https://your-panel.com',
    'ptlc_client_xxxxxxxxxxxxx'
);
```

## Getting Your API Keys

### Admin API Key

1. **Log into your Pterodactyl Panel** as an administrator
2. **Navigate to Admin Panel** → **API Credentials**
3. **Click Create New**
4. **Fill in the required information**:
   - **Description**: A descriptive name for the key
   - **Allowed IPs**: Leave empty for all IPs, or specify allowed IP addresses
5. **Click Create**
6. **Copy the generated API key** (it will only be shown once)

### Client API Key

1. **Log into your Pterodactyl Panel** as a user
2. **Navigate to Account Settings** → **API Credentials**
3. **Click Create New**
4. **Fill in the required information**:
   - **Description**: A descriptive name for the key
   - **Allowed IPs**: Leave empty for all IPs, or specify allowed IPs
5. **Click Create**
6. **Copy the generated API key** (it will only be shown once)

## Testing Your Connection

### Test Admin API

```php
<?php
try {
    $servers = $sdk->admin()->servers()->listServers();
    echo "✅ Admin API connection successful!";
    echo "Found " . count($servers['data']) . " servers";
} catch (Exception $e) {
    echo "❌ Admin API connection failed: " . $e->getMessage();
}
```

### Test Client API

```php
<?php
try {
    $account = $sdk->client()->getAccountDetails();
    echo "✅ Client API connection successful!";
    echo "Logged in as: " . $account['attributes']['email'];
} catch (Exception $e) {
    echo "❌ Client API connection failed: " . $e->getMessage();
}
```

## Your First API Calls

### Admin API - List All Servers

```php
<?php
try {
    $servers = $sdk->admin()->servers()->listServers();
    
    foreach ($servers['data'] as $server) {
        echo "Server: " . $server['attributes']['name'] . "\n";
        echo "Status: " . $server['attributes']['status'] . "\n";
        echo "Owner: " . $server['attributes']['user'] . "\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Client API - Get Account Details

```php
<?php
try {
    $account = $sdk->client()->getAccountDetails();
    
    echo "Email: " . $account['attributes']['email'] . "\n";
    echo "Username: " . $account['attributes']['username'] . "\n";
    echo "First Name: " . $account['attributes']['first_name'] . "\n";
    echo "Last Name: " . $account['attributes']['last_name'] . "\n";
    echo "Created: " . $account['attributes']['created_at'] . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Client API - List User's Servers

```php
<?php
try {
    $servers = $sdk->client()->servers()->listServers();
    
    foreach ($servers['data'] as $server) {
        echo "Server: " . $server['attributes']['name'] . "\n";
        echo "Identifier: " . $server['attributes']['identifier'] . "\n";
        echo "Status: " . $server['attributes']['status'] . "\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Common Configuration

### Environment Variables

```php
<?php
// Using environment variables
$sdk = new PterodactylSDK(
    $_ENV['PTERODACTYL_PANEL_URL'],
    $_ENV['PTERODACTYL_ADMIN_KEY'],
    $_ENV['PTERODACTYL_CLIENT_KEY']
);
```

### Configuration File

```php
<?php
// config.php
return [
    'panel' => [
        'url' => 'https://your-panel.com',
        'admin_key' => 'ptlc_admin_xxxxxxxxxxxxx',
        'client_key' => 'ptlc_client_xxxxxxxxxxxxx'
    ]
];

// Usage
$config = require 'config.php';
$sdk = new PterodactylSDK(
    $config['panel']['url'],
    $config['panel']['admin_key'],
    $config['panel']['client_key']
);
```

### Configuration Class

```php
<?php
class PanelConfig {
    private string $url;
    private string $adminKey;
    private string $clientKey;
    
    public function __construct(string $url, string $adminKey, string $clientKey) {
        $this->url = $url;
        $this->adminKey = $adminKey;
        $this->clientKey = $clientKey;
    }
    
    public function createSDK(): PterodactylSDK {
        return new PterodactylSDK($this->url, $this->adminKey, $this->clientKey);
    }
    
    public function createAdminOnly(): PterodactylAdmin {
        return PterodactylSDK::adminOnly($this->url, $this->adminKey);
    }
    
    public function createClientOnly(): PterodactylClient {
        return PterodactylSDK::clientOnly($this->url, $this->clientKey);
    }
}

// Usage
$config = new PanelConfig(
    'https://your-panel.com',
    'ptlc_admin_xxxxxxxxxxxxx',
    'ptlc_client_xxxxxxxxxxxxx'
);
$sdk = $config->createSDK();
```

## Error Handling

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PermissionException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ResourceNotFoundException;

try {
    $servers = $sdk->admin()->servers()->listServers();
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (PermissionException $e) {
    echo "Permission denied: " . $e->getMessage();
} catch (ResourceNotFoundException $e) {
    echo "Resource not found: " . $e->getMessage();
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage();
}
```

## Security Best Practices

### 1. Store API Keys Securely

```php
<?php
// ❌ Don't hardcode API keys
$sdk = new PterodactylSDK('url', 'hardcoded-admin-key', 'hardcoded-client-key');

// ✅ Use environment variables
$sdk = new PterodactylSDK(
    $_ENV['PTERODACTYL_PANEL_URL'],
    $_ENV['PTERODACTYL_ADMIN_KEY'],
    $_ENV['PTERODACTYL_CLIENT_KEY']
);
```

### 2. Use HTTPS

```php
<?php
// ✅ Always use HTTPS in production
$sdk = new PterodactylSDK(
    'https://your-panel.com',  // Use HTTPS
    $adminKey,
    $clientKey
);
```

### 3. Validate API Keys

```php
<?php
function validateApiKeys($sdk): array {
    $result = [
        'admin' => false,
        'client' => false,
        'errors' => []
    ];
    
    try {
        $sdk->admin()->servers()->listServers();
        $result['admin'] = true;
    } catch (Exception $e) {
        $result['errors']['admin'] = $e->getMessage();
    }
    
    try {
        $sdk->client()->getAccountDetails();
        $result['client'] = true;
    } catch (Exception $e) {
        $result['errors']['client'] = $e->getMessage();
    }
    
    return $result;
}

// Usage
$validation = validateApiKeys($sdk);
if ($validation['admin'] && $validation['client']) {
    echo "✅ Both API keys are valid";
} else {
    echo "❌ API key validation failed";
    foreach ($validation['errors'] as $api => $error) {
        echo "$api API: $error\n";
    }
}
```

## Next Steps

Now that you have the Panel API set up, explore:

- [Authentication](authentication.md) - Understanding Panel API authentication
- [Admin API](admin-api.md) - Administrative operations
- [Client API](client-api.md) - User operations
- [Error Handling](error-handling.md) - Comprehensive error management
- [Examples](examples.md) - Real-world usage examples
