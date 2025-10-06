# Wings API - Error Handling

The Wings API provides comprehensive error handling with specific exception types for different error scenarios.

## Exception Types

### WingsAuthenticationException

Thrown when authentication fails (invalid token, expired token, etc.).

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsAuthenticationException;

try {
    $system = $wings->getSystem()->getSystemInfo();
} catch (WingsAuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
    // Handle authentication error
    // Maybe refresh token or redirect to login
}
```

### WingsConnectionException

Thrown when connection to Wings daemon fails (network issues, daemon down, etc.).

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;

try {
    $system = $wings->getSystem()->getSystemInfo();
} catch (WingsConnectionException $e) {
    echo "Connection failed: " . $e->getMessage();
    // Handle connection error
    // Maybe retry or show maintenance message
}
```

### WingsRequestException

Thrown when Wings daemon returns an error (4xx/5xx status codes).

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;

try {
    $server = $wings->getServer()->getServer('invalid-uuid');
} catch (WingsRequestException $e) {
    echo "Request failed: " . $e->getMessage();
    // Handle request error
    // Maybe show user-friendly message
}
```

### WingsException

Base exception class for all Wings-specific exceptions. Can be used as a catch-all.

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsException;

try {
    $system = $wings->getSystem()->getSystemInfo();
} catch (WingsException $e) {
    echo "Wings error: " . $e->getMessage();
    // Handle any Wings-specific error
}
```

## Comprehensive Error Handling

Here's a complete example showing how to handle all possible exceptions:

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsAuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsException;

function handleWingsApiCall($callback) {
    try {
        return $callback();
    } catch (WingsAuthenticationException $e) {
        // Log authentication failure
        error_log("Wings authentication failed: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Authentication failed. Please check your Wings token.',
            'code' => 'AUTH_FAILED',
            'retry' => false
        ];
        
    } catch (WingsConnectionException $e) {
        // Log connection failure
        error_log("Wings connection failed: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Unable to connect to Wings daemon. Please try again later.',
            'code' => 'CONNECTION_FAILED',
            'retry' => true
        ];
        
    } catch (WingsRequestException $e) {
        // Log request failure
        error_log("Wings request failed: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'Request failed. Please check your parameters.',
            'code' => 'REQUEST_FAILED',
            'retry' => false
        ];
        
    } catch (WingsException $e) {
        // Log any other Wings-specific error
        error_log("Wings error: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'An error occurred while communicating with Wings.',
            'code' => 'WINGS_ERROR',
            'retry' => false
        ];
        
    } catch (Exception $e) {
        // Log any other unexpected error
        error_log("Unexpected Wings error: " . $e->getMessage());
        
        return [
            'success' => false,
            'error' => 'An unexpected error occurred.',
            'code' => 'UNEXPECTED_ERROR',
            'retry' => false
        ];
    }
}

// Usage example
$result = handleWingsApiCall(function() {
    return $wings->getSystem()->getSystemInfo();
});

if ($result['success']) {
    // Handle successful response
    $systemInfo = $result;
} else {
    // Handle error
    echo "Error: " . $result['error'];
    if (isset($result['code'])) {
        echo " (Code: " . $result['code'] . ")";
    }
    if ($result['retry']) {
        echo " - You may want to retry this operation.";
    }
}
```

## Retry Logic

For transient errors like connection failures, you might want to implement retry logic:

```php
<?php
function retryWingsApiCall($callback, $maxRetries = 3, $baseDelay = 1) {
    $attempt = 0;
    
    while ($attempt < $maxRetries) {
        try {
            return $callback();
        } catch (WingsConnectionException $e) {
            $attempt++;
            if ($attempt >= $maxRetries) {
                throw $e;
            }
            
            $delay = $baseDelay * pow(2, $attempt); // Exponential backoff
            echo "Connection failed, retrying in {$delay} seconds... (attempt {$attempt}/{$maxRetries})\n";
            sleep($delay);
            
        } catch (WingsRequestException $e) {
            // Don't retry for request errors (4xx/5xx)
            throw $e;
        } catch (Exception $e) {
            // Don't retry for other exceptions
            throw $e;
        }
    }
}

// Usage
$systemInfo = retryWingsApiCall(function() {
    return $wings->getSystem()->getSystemInfo();
});
```

## Error Response Format

When handling errors in your application, consider returning a consistent error response format:

```php
<?php
function formatWingsErrorResponse($exception) {
    $response = [
        'success' => false,
        'error' => [
            'message' => $exception->getMessage(),
            'type' => get_class($exception),
            'timestamp' => date('c')
        ]
    ];
    
    if ($exception instanceof WingsRequestException) {
        $response['error']['status_code'] = $exception->getStatusCode();
        $response['error']['response_body'] = $exception->getResponseBody();
    }
    
    if ($exception instanceof WingsConnectionException) {
        $response['error']['connection_details'] = [
            'host' => $exception->getHost(),
            'port' => $exception->getPort()
        ];
    }
    
    return $response;
}
```

## Logging Best Practices

```php
<?php
use Psr\Log\LoggerInterface;

class WingsService {
    private $wings;
    private $logger;
    
    public function __construct($wings, LoggerInterface $logger) {
        $this->wings = $wings;
        $this->logger = $logger;
    }
    
    public function getSystemInfo() {
        try {
            $info = $this->wings->getSystem()->getSystemInfo();
            $this->logger->info('Successfully retrieved Wings system info', [
                'wings_version' => $info['version'] ?? 'unknown'
            ]);
            return $info;
            
        } catch (WingsAuthenticationException $e) {
            $this->logger->error('Wings authentication failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
            
        } catch (WingsConnectionException $e) {
            $this->logger->error('Wings connection failed', [
                'message' => $e->getMessage(),
                'host' => $e->getHost(),
                'port' => $e->getPort(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
            
        } catch (Exception $e) {
            $this->logger->error('Unexpected error retrieving Wings system info', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
```

## Connection Health Check

```php
<?php
function checkWingsHealth($wings): array {
    $health = [
        'status' => 'unknown',
        'details' => [],
        'timestamp' => time()
    ];
    
    try {
        // Test basic connection
        if (!$wings->testConnection()) {
            $health['status'] = 'unhealthy';
            $health['details']['connection'] = 'Connection test failed';
            return $health;
        }
        
        // Test API call
        $systemInfo = $wings->getSystem()->getSystemInfo();
        $health['status'] = 'healthy';
        $health['details'] = [
            'connection' => 'OK',
            'wings_version' => $systemInfo['version'] ?? 'unknown',
            'docker_version' => $systemInfo['docker_version'] ?? 'unknown'
        ];
        
    } catch (WingsAuthenticationException $e) {
        $health['status'] = 'auth_failed';
        $health['details']['error'] = 'Authentication failed: ' . $e->getMessage();
        
    } catch (WingsConnectionException $e) {
        $health['status'] = 'connection_failed';
        $health['details']['error'] = 'Connection failed: ' . $e->getMessage();
        
    } catch (Exception $e) {
        $health['status'] = 'error';
        $health['details']['error'] = 'Unexpected error: ' . $e->getMessage();
    }
    
    return $health;
}

// Usage
$health = checkWingsHealth($wings);
echo "Wings Health: " . strtoupper($health['status']) . "\n";
foreach ($health['details'] as $key => $value) {
    echo "  $key: $value\n";
}
```

## Error Monitoring

```php
<?php
class WingsErrorMonitor {
    private $errors = [];
    private $maxErrors = 100;
    
    public function logError($exception, $context = []) {
        $error = [
            'timestamp' => time(),
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'context' => $context
        ];
        
        $this->errors[] = $error;
        
        // Keep only the last N errors
        if (count($this->errors) > $this->maxErrors) {
            $this->errors = array_slice($this->errors, -$this->maxErrors);
        }
    }
    
    public function getErrorStats(): array {
        $stats = [
            'total_errors' => count($this->errors),
            'error_types' => [],
            'recent_errors' => array_slice($this->errors, -10)
        ];
        
        foreach ($this->errors as $error) {
            $type = $error['type'];
            $stats['error_types'][$type] = ($stats['error_types'][$type] ?? 0) + 1;
        }
        
        return $stats;
    }
    
    public function clearErrors(): void {
        $this->errors = [];
    }
}

// Usage
$monitor = new WingsErrorMonitor();

try {
    $system = $wings->getSystem()->getSystemInfo();
} catch (Exception $e) {
    $monitor->logError($e, ['operation' => 'getSystemInfo']);
    throw $e;
}

// Get error statistics
$stats = $monitor->getErrorStats();
echo "Total errors: " . $stats['total_errors'] . "\n";
foreach ($stats['error_types'] as $type => $count) {
    echo "$type: $count\n";
}
```

## Best Practices

1. **Always handle exceptions** - Never let Wings exceptions bubble up unhandled
2. **Log errors appropriately** - Use appropriate log levels and include context
3. **Implement retry logic** - For transient errors like connection failures
4. **Provide user-friendly messages** - Don't expose internal error details to users
5. **Monitor error rates** - Track error patterns and alert on high error rates
6. **Test error scenarios** - Write tests for various error conditions
7. **Use structured logging** - Include relevant context in error logs
8. **Implement circuit breakers** - Prevent cascading failures in distributed systems
