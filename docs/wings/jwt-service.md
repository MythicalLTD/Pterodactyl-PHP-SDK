# Wings API - JWT Service

The JWT Service provides secure token generation for various Wings operations, including server access, file operations, and authentication.

## Getting Started

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$wings = PterodactylSDK::wingsOnly('wings.example.com', 8080, 'https', 'token');
$jwt = $wings->getJwt();
```

## Token Generation

### Generate Server Token

```php
<?php
$serverUuid = 'server-uuid-here';
$token = $jwt->generateServerToken($serverUuid);

echo "Server Token: " . $token . "\n";
echo "Token expires in: 1 hour\n";
```

### Generate File Token

```php
<?php
$serverUuid = 'server-uuid-here';
$filePath = '/path/to/file.txt';
$token = $jwt->generateFileToken($serverUuid, $filePath);

echo "File Token: " . $token . "\n";
echo "File Path: " . $filePath . "\n";
echo "Token expires in: 1 hour\n";
```

### Generate Console Token

```php
<?php
$serverUuid = 'server-uuid-here';
$token = $jwt->generateConsoleToken($serverUuid);

echo "Console Token: " . $token . "\n";
echo "Token expires in: 1 hour\n";
```

### Generate Download Token

```php
<?php
$serverUuid = 'server-uuid-here';
$filePath = '/path/to/download/file.zip';
$token = $jwt->generateDownloadToken($serverUuid, $filePath);

echo "Download Token: " . $token . "\n";
echo "File Path: " . $filePath . "\n";
echo "Token expires in: 1 hour\n";
```

## Custom Token Generation

### Generate Token with Custom Expiry

```php
<?php
$serverUuid = 'server-uuid-here';
$expiry = 3600; // 1 hour in seconds
$token = $jwt->generateServerToken($serverUuid, $expiry);

echo "Server Token: " . $token . "\n";
echo "Token expires in: " . $expiry . " seconds\n";
```

### Generate Token with Custom Claims

```php
<?php
$serverUuid = 'server-uuid-here';
$claims = [
    'server_uuid' => $serverUuid,
    'permissions' => ['file.read', 'file.write'],
    'user_id' => 123
];
$expiry = 7200; // 2 hours

$token = $jwt->generateCustomToken($claims, $expiry);
echo "Custom Token: " . $token . "\n";
```

## Token Validation

### Validate Token

```php
<?php
function validateToken($jwt, $token): array {
    $validation = [
        'valid' => false,
        'expired' => false,
        'claims' => [],
        'error' => null
    ];
    
    try {
        $claims = $jwt->validateToken($token);
        $validation['valid'] = true;
        $validation['claims'] = $claims;
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'expired') !== false) {
            $validation['expired'] = true;
        }
        $validation['error'] = $e->getMessage();
    }
    
    return $validation;
}

// Usage
$token = 'your-jwt-token-here';
$validation = validateToken($jwt, $token);

if ($validation['valid']) {
    echo "✅ Token is valid\n";
    echo "Claims: " . json_encode($validation['claims']) . "\n";
} elseif ($validation['expired']) {
    echo "⚠️ Token has expired\n";
} else {
    echo "❌ Token is invalid: " . $validation['error'] . "\n";
}
```

### Get Token Claims

```php
<?php
$token = 'your-jwt-token-here';
$claims = $jwt->getTokenClaims($token);

echo "Token Claims:\n";
foreach ($claims as $key => $value) {
    echo "  $key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
}
```

## Token Management

### Token Expiry Check

```php
<?php
function isTokenExpired($jwt, $token): bool {
    try {
        $claims = $jwt->getTokenClaims($token);
        $expiry = $claims['exp'] ?? 0;
        return time() > $expiry;
    } catch (Exception $e) {
        return true; // Consider invalid tokens as expired
    }
}

// Usage
$token = 'your-jwt-token-here';
if (isTokenExpired($jwt, $token)) {
    echo "⚠️ Token has expired, generating new one...\n";
    $newToken = $jwt->generateServerToken($serverUuid);
} else {
    echo "✅ Token is still valid\n";
}
```

### Token Refresh

```php
<?php
function refreshTokenIfNeeded($jwt, $token, $serverUuid): string {
    if (isTokenExpired($jwt, $token)) {
        echo "🔄 Refreshing expired token...\n";
        return $jwt->generateServerToken($serverUuid);
    }
    
    return $token;
}

// Usage
$token = 'your-jwt-token-here';
$refreshedToken = refreshTokenIfNeeded($jwt, $token, $serverUuid);
```

## Secure Token Usage

### Secure File Access

```php
<?php
function secureFileAccess($jwt, $serverUuid, $filePath): array {
    $result = [
        'success' => false,
        'token' => null,
        'url' => null,
        'error' => null
    ];
    
    try {
        // Generate file token
        $token = $jwt->generateFileToken($serverUuid, $filePath);
        
        // Construct secure URL
        $url = "https://wings.example.com/api/servers/{$serverUuid}/files/download?token={$token}";
        
        $result['success'] = true;
        $result['token'] = $token;
        $result['url'] = $url;
        
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
    }
    
    return $result;
}

// Usage
$fileAccess = secureFileAccess($jwt, $serverUuid, '/path/to/file.txt');
if ($fileAccess['success']) {
    echo "✅ Secure file access URL: " . $fileAccess['url'] . "\n";
} else {
    echo "❌ Failed to generate secure file access: " . $fileAccess['error'] . "\n";
}
```

### Secure Console Access

```php
<?php
function secureConsoleAccess($jwt, $serverUuid): array {
    $result = [
        'success' => false,
        'token' => null,
        'websocket_url' => null,
        'error' => null
    ];
    
    try {
        // Generate console token
        $token = $jwt->generateConsoleToken($serverUuid);
        
        // Construct WebSocket URL
        $websocketUrl = "wss://wings.example.com/api/servers/{$serverUuid}/ws?token={$token}";
        
        $result['success'] = true;
        $result['token'] = $token;
        $result['websocket_url'] = $websocketUrl;
        
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
    }
    
    return $result;
}

// Usage
$consoleAccess = secureConsoleAccess($jwt, $serverUuid);
if ($consoleAccess['success']) {
    echo "✅ Secure console WebSocket URL: " . $consoleAccess['websocket_url'] . "\n";
} else {
    echo "❌ Failed to generate secure console access: " . $consoleAccess['error'] . "\n";
}
```

## Token Security Best Practices

### 1. Token Storage

```php
<?php
class SecureTokenManager {
    private $jwt;
    private $tokens = [];
    
    public function __construct($jwt) {
        $this->jwt = $jwt;
    }
    
    public function getServerToken($serverUuid): string {
        // Check if we have a valid cached token
        if (isset($this->tokens[$serverUuid])) {
            $token = $this->tokens[$serverUuid];
            if (!$this->isTokenExpired($token)) {
                return $token;
            }
        }
        
        // Generate new token
        $token = $this->jwt->generateServerToken($serverUuid);
        $this->tokens[$serverUuid] = $token;
        
        return $token;
    }
    
    private function isTokenExpired($token): bool {
        try {
            $claims = $this->jwt->getTokenClaims($token);
            $expiry = $claims['exp'] ?? 0;
            return time() > ($expiry - 300); // Refresh 5 minutes before expiry
        } catch (Exception $e) {
            return true;
        }
    }
    
    public function clearTokens(): void {
        $this->tokens = [];
    }
}

// Usage
$tokenManager = new SecureTokenManager($jwt);
$token = $tokenManager->getServerToken($serverUuid);
```

### 2. Token Rotation

```php
<?php
function rotateTokens($jwt, $serverUuid): array {
    $tokens = [];
    
    // Generate new tokens
    $tokens['server'] = $jwt->generateServerToken($serverUuid);
    $tokens['console'] = $jwt->generateConsoleToken($serverUuid);
    $tokens['file'] = $jwt->generateFileToken($serverUuid, '/');
    
    return $tokens;
}

// Usage
$newTokens = rotateTokens($jwt, $serverUuid);
echo "✅ Tokens rotated successfully\n";
```

## Error Handling

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;

try {
    $token = $jwt->generateServerToken($serverUuid);
    echo "Token generated successfully\n";
} catch (WingsRequestException $e) {
    echo "Request failed: " . $e->getMessage() . "\n";
} catch (WingsConnectionException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
```

## Example: JWT Token Dashboard

```php
<?php
function generateJwtDashboard($jwt, $serverUuid): string {
    $dashboard = "🔐 JWT Token Dashboard\n";
    $dashboard .= "====================\n\n";
    
    try {
        // Generate various tokens
        $serverToken = $jwt->generateServerToken($serverUuid);
        $consoleToken = $jwt->generateConsoleToken($serverUuid);
        $fileToken = $jwt->generateFileToken($serverUuid, '/');
        
        $dashboard .= "🎫 Generated Tokens:\n";
        $dashboard .= "  Server Token: " . substr($serverToken, 0, 20) . "...\n";
        $dashboard .= "  Console Token: " . substr($consoleToken, 0, 20) . "...\n";
        $dashboard .= "  File Token: " . substr($fileToken, 0, 20) . "...\n\n";
        
        // Token info
        $claims = $jwt->getTokenClaims($serverToken);
        $dashboard .= "📋 Token Information:\n";
        $dashboard .= "  Issued At: " . date('Y-m-d H:i:s', $claims['iat']) . "\n";
        $dashboard .= "  Expires At: " . date('Y-m-d H:i:s', $claims['exp']) . "\n";
        $dashboard .= "  Server UUID: " . $claims['server_uuid'] . "\n";
        
    } catch (Exception $e) {
        $dashboard .= "❌ Error generating dashboard: " . $e->getMessage() . "\n";
    }
    
    return $dashboard;
}

// Generate and display dashboard
echo generateJwtDashboard($jwt, $serverUuid);
```
