# Wings API - Authentication

Wings uses a token-based authentication system with node tokens from your Pterodactyl panel.

## Token Format

Wings tokens are composed of two parts separated by a dot:

```
node-token-id.node-token-secret
```

### Getting Your Token

1. **Log into Pterodactyl Panel** as an administrator
2. **Navigate to Admin Panel** → **Nodes**
3. **Click on your node** to view details
4. **Find the Node Token section**
5. **Copy the Node Token ID** and **Node Token Secret**
6. **Combine them** with a dot separator

Example:
```
Node Token ID: abc123def456ghi789
Node Token Secret: xyz789uvw012rst345
Wings Token: abc123def456ghi789.xyz789uvw012rst345
```

## Using Tokens

### Basic Usage

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$wings = PterodactylSDK::wingsOnly(
    'wings.example.com',
    8080,
    'https',
    'abc123def456ghi789.xyz789uvw012rst345'
);
```

### Environment Variables

```php
<?php
// .env file
WINGS_HOST=wings.example.com
WINGS_PORT=8080
WINGS_PROTOCOL=https
WINGS_TOKEN=abc123def456ghi789.xyz789uvw012rst345

// Usage
$wings = PterodactylSDK::wingsOnly(
    $_ENV['WINGS_HOST'],
    (int)$_ENV['WINGS_PORT'],
    $_ENV['WINGS_PROTOCOL'],
    $_ENV['WINGS_TOKEN']
);
```

### Configuration Class

```php
<?php
class WingsConfig {
    private string $host;
    private int $port;
    private string $protocol;
    private string $token;
    
    public function __construct(
        string $host,
        int $port,
        string $protocol,
        string $token
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->token = $token;
    }
    
    public function createClient(): Wings {
        return PterodactylSDK::wingsOnly(
            $this->host,
            $this->port,
            $this->protocol,
            $this->token
        );
    }
}

// Usage
$config = new WingsConfig(
    'wings.example.com',
    8080,
    'https',
    'abc123def456ghi789.xyz789uvw012rst345'
);
$wings = $config->createClient();
```

## Token Management

### Updating Tokens

```php
<?php
// Update token after initialization
$wings->setAuthToken('new-node-token-id.new-node-token-secret');

// Get current token
$currentToken = $wings->getAuthToken();
echo "Current token: " . $currentToken;
```

### Token Validation

```php
<?php
function validateWingsToken(string $token): bool {
    // Check if token has the correct format
    if (strpos($token, '.') === false) {
        return false;
    }
    
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        return false;
    }
    
    // Check if both parts are not empty
    return !empty($parts[0]) && !empty($parts[1]);
}

// Usage
$token = 'abc123def456ghi789.xyz789uvw012rst345';
if (validateWingsToken($token)) {
    echo "Token format is valid";
} else {
    echo "Invalid token format";
}
```

## Security Best Practices

### 1. Store Tokens Securely

```php
<?php
// ❌ Don't hardcode tokens
$wings = PterodactylSDK::wingsOnly('host', 8080, 'https', 'hardcoded-token');

// ✅ Use environment variables
$wings = PterodactylSDK::wingsOnly(
    $_ENV['WINGS_HOST'],
    (int)$_ENV['WINGS_PORT'],
    $_ENV['WINGS_PROTOCOL'],
    $_ENV['WINGS_TOKEN']
);
```

### 2. Rotate Tokens Regularly

```php
<?php
// Generate new token in Pterodactyl panel, then update
$wings->setAuthToken($newToken);
```

### 3. Use HTTPS in Production

```php
<?php
// ✅ Always use HTTPS in production
$wings = PterodactylSDK::wingsOnly(
    'wings.example.com',
    8080,
    'https',  // Use HTTPS
    $token
);
```

### 4. Validate Connections

```php
<?php
function createSecureWingsClient(string $host, string $token): ?Wings {
    try {
        $wings = PterodactylSDK::wingsOnly($host, 8080, 'https', $token);
        
        // Test connection
        if (!$wings->testConnection()) {
            throw new Exception('Connection test failed');
        }
        
        return $wings;
    } catch (Exception $e) {
        error_log("Wings connection failed: " . $e->getMessage());
        return null;
    }
}
```

## Error Handling

### Authentication Errors

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsAuthenticationException;

try {
    $wings = PterodactylSDK::wingsOnly('host', 8080, 'https', 'invalid-token');
    $system = $wings->getSystem()->getSystemInfo();
} catch (WingsAuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
    // Handle authentication error
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage();
}
```

### Connection Errors

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;

try {
    $wings = PterodactylSDK::wingsOnly('invalid-host', 8080, 'https', $token);
    $system = $wings->getSystem()->getSystemInfo();
} catch (WingsConnectionException $e) {
    echo "Connection failed: " . $e->getMessage();
    // Handle connection error
}
```

## Testing Authentication

```php
<?php
function testWingsAuthentication(string $host, string $token): array {
    $result = [
        'success' => false,
        'message' => '',
        'details' => []
    ];
    
    try {
        $wings = PterodactylSDK::wingsOnly($host, 8080, 'https', $token);
        
        // Test connection
        if (!$wings->testConnection()) {
            $result['message'] = 'Connection test failed';
            return $result;
        }
        
        // Test API call
        $system = $wings->getSystem()->getSystemInfo();
        $result['success'] = true;
        $result['message'] = 'Authentication successful';
        $result['details'] = [
            'wings_version' => $system['version'] ?? 'Unknown',
            'docker_version' => $system['docker_version'] ?? 'Unknown'
        ];
        
    } catch (WingsAuthenticationException $e) {
        $result['message'] = 'Authentication failed: ' . $e->getMessage();
    } catch (WingsConnectionException $e) {
        $result['message'] = 'Connection failed: ' . $e->getMessage();
    } catch (Exception $e) {
        $result['message'] = 'Unexpected error: ' . $e->getMessage();
    }
    
    return $result;
}

// Usage
$test = testWingsAuthentication('wings.example.com', $token);
if ($test['success']) {
    echo "✅ Authentication successful!";
    echo "Wings Version: " . $test['details']['wings_version'];
} else {
    echo "❌ Authentication failed: " . $test['message'];
}
```
