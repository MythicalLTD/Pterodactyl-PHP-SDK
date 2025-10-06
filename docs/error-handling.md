# Error Handling

The Pterodactyl PHP SDK provides comprehensive error handling with specific exception types for different error scenarios.

## Exception Types

### AuthenticationException

Thrown when authentication fails (invalid API key, expired token, etc.).

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;

try {
    $servers = $sdk->admin()->servers()->listServers();
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
    // Handle authentication error
    // Maybe redirect to login or refresh API key
}
```

### PermissionException

Thrown when the API key doesn't have sufficient permissions for the requested operation.

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\PermissionException;

try {
    $server = $sdk->admin()->servers()->createServer($data);
} catch (PermissionException $e) {
    echo "Permission denied: " . $e->getMessage();
    // Handle permission error
    // Maybe show user-friendly message or log for admin review
}
```

### ResourceNotFoundException

Thrown when a requested resource (server, user, node, etc.) doesn't exist.

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\ResourceNotFoundException;

try {
    $server = $sdk->admin()->servers()->getServer(999);
} catch (ResourceNotFoundException $e) {
    echo "Resource not found: " . $e->getMessage();
    // Handle not found error
    // Maybe show 404 page or redirect to list
}
```

### ValidationException

Thrown when input data validation fails (invalid parameters, missing required fields, etc.).

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\ValidationException;

try {
    $server = $sdk->admin()->servers()->createServer([
        'name' => '', // Invalid: empty name
        'user' => 'invalid' // Invalid: should be integer
    ]);
} catch (ValidationException $e) {
    echo "Validation failed: " . $e->getMessage();
    $errors = $e->getErrors(); // Get detailed validation errors
    foreach ($errors as $field => $messages) {
        echo "Field '$field': " . implode(', ', $messages) . "\n";
    }
}
```

### RateLimitException

Thrown when the API rate limit is exceeded.

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\RateLimitException;

try {
    $servers = $sdk->admin()->servers()->listServers();
} catch (RateLimitException $e) {
    echo "Rate limit exceeded: " . $e->getMessage();
    $retryAfter = $e->getRetryAfter(); // Get seconds to wait
    echo "Retry after: " . $retryAfter . " seconds";
    
    // Implement exponential backoff
    sleep($retryAfter);
}
```

### ServerException

Thrown when the Pterodactyl server returns an error (5xx status codes).

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\ServerException;

try {
    $servers = $sdk->admin()->servers()->listServers();
} catch (ServerException $e) {
    echo "Server error: " . $e->getMessage();
    // Handle server error
    // Maybe retry later or show maintenance message
}
```

### PterodactylException

Base exception class for all SDK-specific exceptions. Can be used as a catch-all.

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\PterodactylException;

try {
    $servers = $sdk->admin()->servers()->listServers();
} catch (PterodactylException $e) {
    echo "Pterodactyl error: " . $e->getMessage();
    // Handle any Pterodactyl-specific error
}
```

## Comprehensive Error Handling

Here's a complete example showing how to handle all possible exceptions:

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PermissionException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ResourceNotFoundException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ValidationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\RateLimitException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ServerException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PterodactylException;

function handleApiCall($callback) {
    try {
        return $callback();
    } catch (AuthenticationException $e) {
        // Log authentication failure
        error_log("Authentication failed: " . $e->getMessage());
        
        // Redirect to login or refresh token
        return [
            'success' => false,
            'error' => 'Authentication failed. Please check your API key.',
            'code' => 'AUTH_FAILED'
        ];
        
    } catch (PermissionException $e) {
        // Log permission denial
        error_log("Permission denied: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'You do not have permission to perform this action.',
            'code' => 'PERMISSION_DENIED'
        ];
        
    } catch (ResourceNotFoundException $e) {
        // Log resource not found
        error_log("Resource not found: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'The requested resource was not found.',
            'code' => 'NOT_FOUND'
        ];
        
    } catch (ValidationException $e) {
        // Log validation errors
        error_log("Validation failed: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Invalid input data.',
            'code' => 'VALIDATION_FAILED',
            'errors' => $e->getErrors()
        ];
        
    } catch (RateLimitException $e) {
        // Log rate limit
        error_log("Rate limit exceeded: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Too many requests. Please try again later.',
            'code' => 'RATE_LIMITED',
            'retry_after' => $e->getRetryAfter()
        ];
        
    } catch (ServerException $e) {
        // Log server error
        error_log("Server error: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Server error. Please try again later.',
            'code' => 'SERVER_ERROR'
        ];
        
    } catch (PterodactylException $e) {
        // Log any other Pterodactyl-specific error
        error_log("Pterodactyl error: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'An error occurred while communicating with the server.',
            'code' => 'PTERODACTYL_ERROR'
        ];
        
    } catch (Exception $e) {
        // Log any other unexpected error
        error_log("Unexpected error: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'An unexpected error occurred.',
            'code' => 'UNEXPECTED_ERROR'
        ];
    }
}

// Usage example
$result = handleApiCall(function() {
    return $sdk->admin()->servers()->listServers();
});

if ($result['success']) {
    // Handle successful response
    $servers = $result;
} else {
    // Handle error
    echo "Error: " . $result['error'];
    if (isset($result['code'])) {
        echo " (Code: " . $result['code'] . ")";
    }
}
```

## Retry Logic

For transient errors like rate limiting or server errors, you might want to implement retry logic:

```php
<?php
function retryApiCall($callback, $maxRetries = 3, $baseDelay = 1) {
    $attempt = 0;
    
    while ($attempt < $maxRetries) {
        try {
            return $callback();
        } catch (RateLimitException $e) {
            $attempt++;
            if ($attempt >= $maxRetries) {
                throw $e;
            }
            
            $delay = $e->getRetryAfter() ?: ($baseDelay * pow(2, $attempt));
            sleep($delay);
            
        } catch (ServerException $e) {
            $attempt++;
            if ($attempt >= $maxRetries) {
                throw $e;
            }
            
            $delay = $baseDelay * pow(2, $attempt);
            sleep($delay);
            
        } catch (Exception $e) {
            // Don't retry for other exceptions
            throw $e;
        }
    }
}

// Usage
$servers = retryApiCall(function() {
    return $sdk->admin()->servers()->listServers();
});
```

## Logging Best Practices

```php
<?php
use Psr\Log\LoggerInterface;

class PterodactylService {
    private $sdk;
    private $logger;
    
    public function __construct($sdk, LoggerInterface $logger) {
        $this->sdk = $sdk;
        $this->logger = $logger;
    }
    
    public function getServers() {
        try {
            $servers = $this->sdk->admin()->servers()->listServers();
            $this->logger->info('Successfully retrieved servers', [
                'count' => count($servers['data'])
            ]);
            return $servers;
            
        } catch (AuthenticationException $e) {
            $this->logger->error('Authentication failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
            
        } catch (Exception $e) {
            $this->logger->error('Unexpected error retrieving servers', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
```

## Error Response Format

When handling errors in your application, consider returning a consistent error response format:

```php
<?php
function formatErrorResponse($exception) {
    $response = [
        'success' => false,
        'error' => [
            'message' => $exception->getMessage(),
            'type' => get_class($exception),
            'timestamp' => date('c')
        ]
    ];
    
    if ($exception instanceof ValidationException) {
        $response['error']['validation_errors'] = $exception->getErrors();
    }
    
    if ($exception instanceof RateLimitException) {
        $response['error']['retry_after'] = $exception->getRetryAfter();
    }
    
    return $response;
}
```
