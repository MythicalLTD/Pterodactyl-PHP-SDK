# Panel API - Authentication

The Panel API supports two types of API authentication for different levels of access.

## API Key Types

### Admin API Key
- **Purpose**: Administrative operations across the entire panel
- **Access**: All panel resources and management functions
- **Permissions**: Create, read, update, delete servers, users, nodes, locations, nests
- **Generation**: Admin Panel → API Credentials

### Client API Key
- **Purpose**: User-specific operations and server management
- **Access**: User's own servers, files, databases, and account settings
- **Permissions**: Manage user's servers, access files, create databases, manage account
- **Generation**: User Panel → Account Settings → API Credentials

## Getting API Keys

### Admin API Key

1. **Log into Pterodactyl Panel** as an administrator
2. **Navigate to Admin Panel** → **API Credentials**
3. **Click Create New**
4. **Configure the key**:
   - **Description**: Descriptive name (e.g., "Production Admin API")
   - **Allowed IPs**: Leave empty for all IPs, or specify allowed IP addresses
5. **Click Create**
6. **Copy the generated API key** (shown only once)

### Client API Key

1. **Log into Pterodactyl Panel** as a user
2. **Navigate to Account Settings** → **API Credentials**
3. **Click Create New**
4. **Configure the key**:
   - **Description**: Descriptive name (e.g., "My Application API")
   - **Allowed IPs**: Leave empty for all IPs, or specify allowed IPs
5. **Click Create**
6. **Copy the generated API key** (shown only once)

## Using API Keys

### Full SDK (Both Admin and Client)

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$sdk = new PterodactylSDK(
    'https://your-panel.com',        // Panel URL
    'ptlc_admin_xxxxxxxxxxxxx',      // Admin API Key
    'ptlc_client_xxxxxxxxxxxxx'      // Client API Key
);
```

### Admin API Only

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$admin = PterodactylSDK::adminOnly(
    'https://your-panel.com',        // Panel URL
    'ptlc_admin_xxxxxxxxxxxxx'       // Admin API Key
);
```

### Client API Only

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$client = PterodactylSDK::clientOnly(
    'https://your-panel.com',        // Panel URL
    'ptlc_client_xxxxxxxxxxxxx'      // Client API Key
);
```

## Security Best Practices

### 1. Secure Storage

```php
<?php
// ❌ Never hardcode API keys
$sdk = new PterodactylSDK('url', 'hardcoded-key', 'hardcoded-key');

// ✅ Use environment variables
$sdk = new PterodactylSDK(
    $_ENV['PTERODACTYL_PANEL_URL'],
    $_ENV['PTERODACTYL_ADMIN_KEY'],
    $_ENV['PTERODACTYL_CLIENT_KEY']
);
```

### 2. Environment Variables

Create a `.env` file:

```env
PTERODACTYL_PANEL_URL=https://your-panel.com
PTERODACTYL_ADMIN_KEY=ptlc_admin_xxxxxxxxxxxxx
PTERODACTYL_CLIENT_KEY=ptlc_client_xxxxxxxxxxxxx
```

Use with environment variables:

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$sdk = new PterodactylSDK(
    $_ENV['PTERODACTYL_PANEL_URL'],
    $_ENV['PTERODACTYL_ADMIN_KEY'],
    $_ENV['PTERODACTYL_CLIENT_KEY']
);
```

### 3. Configuration Class

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

## Testing Authentication

### Test Admin API

```php
<?php
function testAdminAuthentication($sdk): array {
    $result = [
        'success' => false,
        'message' => '',
        'details' => []
    ];
    
    try {
        $servers = $sdk->admin()->servers()->listServers();
        $result['success'] = true;
        $result['message'] = 'Admin authentication successful';
        $result['details'] = [
            'server_count' => count($servers['data'])
        ];
    } catch (Exception $e) {
        $result['message'] = 'Admin authentication failed: ' . $e->getMessage();
    }
    
    return $result;
}

// Usage
$test = testAdminAuthentication($sdk);
if ($test['success']) {
    echo "✅ Admin API: " . $test['message'];
    echo "Found " . $test['details']['server_count'] . " servers";
} else {
    echo "❌ Admin API: " . $test['message'];
}
```

### Test Client API

```php
<?php
function testClientAuthentication($sdk): array {
    $result = [
        'success' => false,
        'message' => '',
        'details' => []
    ];
    
    try {
        $account = $sdk->client()->getAccountDetails();
        $result['success'] = true;
        $result['message'] = 'Client authentication successful';
        $result['details'] = [
            'email' => $account['attributes']['email'],
            'username' => $account['attributes']['username']
        ];
    } catch (Exception $e) {
        $result['message'] = 'Client authentication failed: ' . $e->getMessage();
    }
    
    return $result;
}

// Usage
$test = testClientAuthentication($sdk);
if ($test['success']) {
    echo "✅ Client API: " . $test['message'];
    echo "Logged in as: " . $test['details']['email'];
} else {
    echo "❌ Client API: " . $test['message'];
}
```

## API Key Management

### Validate API Keys

```php
<?php
function validateApiKeys($sdk): array {
    $validation = [
        'admin' => false,
        'client' => false,
        'errors' => []
    ];
    
    // Test Admin API
    try {
        $sdk->admin()->servers()->listServers();
        $validation['admin'] = true;
    } catch (Exception $e) {
        $validation['errors']['admin'] = $e->getMessage();
    }
    
    // Test Client API
    try {
        $sdk->client()->getAccountDetails();
        $validation['client'] = true;
    } catch (Exception $e) {
        $validation['errors']['client'] = $e->getMessage();
    }
    
    return $validation;
}

// Usage
$validation = validateApiKeys($sdk);
if ($validation['admin'] && $validation['client']) {
    echo "✅ Both API keys are valid";
} else {
    echo "❌ API key validation failed:";
    foreach ($validation['errors'] as $api => $error) {
        echo "$api API: $error\n";
    }
}
```

## Security Considerations

### 1. IP Restrictions
- **Set allowed IPs** when creating API keys for production
- **Use specific IPs** rather than allowing all IPs
- **Monitor IP usage** in the panel

### 2. Key Rotation
- **Rotate keys regularly** (monthly/quarterly)
- **Use descriptive names** for easy identification
- **Monitor key usage** in the panel

### 3. HTTPS Only
```php
<?php
// ✅ Always use HTTPS in production
$sdk = new PterodactylSDK(
    'https://your-panel.com',  // Use HTTPS
    $adminKey,
    $clientKey
);
```

### 4. Error Handling
```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;

try {
    $servers = $sdk->admin()->servers()->listServers();
} catch (AuthenticationException $e) {
    // Handle authentication failure
    error_log("Authentication failed: " . $e->getMessage());
    // Maybe redirect to login or refresh API key
}
```

## Troubleshooting

### Common Issues

1. **Invalid API Key Format**
   - Ensure keys start with `ptlc_admin_` or `ptlc_client_`
   - Check for extra spaces or characters

2. **Permission Denied**
   - Verify the API key has the required permissions
   - Check if the key is for the correct API type (admin vs client)

3. **IP Restrictions**
   - Ensure your server's IP is in the allowed IPs list
   - Check if you're behind a proxy or load balancer

4. **Expired Keys**
   - API keys don't expire, but check if they were revoked
   - Verify the key is still active in the panel

### Debug Authentication

```php
<?php
function debugAuthentication($sdk): void {
    echo "🔍 Debugging Panel API Authentication\n";
    echo "=====================================\n\n";
    
    // Test Admin API
    echo "Testing Admin API...\n";
    try {
        $servers = $sdk->admin()->servers()->listServers();
        echo "✅ Admin API: Success (" . count($servers['data']) . " servers)\n";
    } catch (Exception $e) {
        echo "❌ Admin API: " . $e->getMessage() . "\n";
    }
    
    // Test Client API
    echo "\nTesting Client API...\n";
    try {
        $account = $sdk->client()->getAccountDetails();
        echo "✅ Client API: Success (User: " . $account['attributes']['email'] . ")\n";
    } catch (Exception $e) {
        echo "❌ Client API: " . $e->getMessage() . "\n";
    }
}

// Usage
debugAuthentication($sdk);
```