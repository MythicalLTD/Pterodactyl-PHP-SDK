# Wings API - Examples

This page contains comprehensive examples of common use cases with the Wings API.

## Basic Setup

```php
<?php
require_once 'vendor/autoload.php';

use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsException;

// Initialize Wings client
$wings = PterodactylSDK::wingsOnly(
    'wings.example.com',
    8080,
    'https',
    'node-token-id.node-token-secret'
);
```

## System Monitoring Examples

### Real-time System Dashboard

```php
<?php
function createSystemDashboard($wings) {
    $dashboard = "🖥️ Wings System Dashboard\n";
    $dashboard .= "========================\n\n";
    
    try {
        // System Information
        $info = $wings->getSystem()->getSystemInfo();
        $dashboard .= "📊 System Information:\n";
        $dashboard .= "  Wings Version: {$info['version']}\n";
        $dashboard .= "  Docker Version: {$info['docker_version']}\n";
        $dashboard .= "  System Time: {$info['time']}\n\n";
        
        // Resource Usage
        $stats = $wings->getSystem()->getSystemStats();
        $dashboard .= "📈 Resource Usage:\n";
        $dashboard .= "  CPU: {$stats['cpu']}%\n";
        $dashboard .= "  Memory: {$stats['memory']}%\n";
        $dashboard .= "  Disk: {$stats['disk']}%\n\n";
        
        // System Resources
        $resources = $wings->getSystem()->getSystemResources();
        $dashboard .= "💾 System Resources:\n";
        $dashboard .= "  Total CPU: {$resources['cpu']} cores\n";
        $dashboard .= "  Total Memory: {$resources['memory']} MB\n";
        $dashboard .= "  Total Disk: {$resources['disk']} MB\n";
        $dashboard .= "  Available Memory: {$resources['available_memory']} MB\n";
        $dashboard .= "  Available Disk: {$resources['available_disk']} MB\n";
        
    } catch (WingsException $e) {
        $dashboard .= "❌ Error: " . $e->getMessage() . "\n";
    }
    
    return $dashboard;
}

// Generate and display dashboard
echo createSystemDashboard($wings);
```

### System Health Monitor

```php
<?php
function monitorSystemHealth($wings, $duration = 300) {
    $startTime = time();
    $alerts = [];
    
    while ((time() - $startTime) < $duration) {
        try {
            $stats = $wings->getSystem()->getSystemStats();
            
            // Check for high resource usage
            if ($stats['cpu'] > 80) {
                $alerts[] = "High CPU usage: {$stats['cpu']}%";
            }
            
            if ($stats['memory'] > 85) {
                $alerts[] = "High memory usage: {$stats['memory']}%";
            }
            
            if ($stats['disk'] > 90) {
                $alerts[] = "High disk usage: {$stats['disk']}%";
            }
            
            // Display current status
            echo "[" . date('H:i:s') . "] CPU: {$stats['cpu']}% | Memory: {$stats['memory']}% | Disk: {$stats['disk']}%\n";
            
            if (!empty($alerts)) {
                echo "⚠️ Alerts: " . implode(', ', $alerts) . "\n";
                $alerts = []; // Clear alerts after displaying
            }
            
            sleep(30); // Check every 30 seconds
        } catch (WingsException $e) {
            echo "❌ Error monitoring system: " . $e->getMessage() . "\n";
            sleep(60); // Wait longer on error
        }
    }
}

// Monitor system for 5 minutes
monitorSystemHealth($wings, 300);
```

## Server Management Examples

### Server Lifecycle Management

```php
<?php
function manageServerLifecycle($wings, $serverUuid) {
    $server = $wings->getServer();
    
    try {
        // Get server details
        $serverDetails = $server->getServer($serverUuid);
        echo "Server: {$serverDetails['name']} ({$serverDetails['uuid']})\n";
        echo "Current Status: {$serverDetails['state']}\n\n";
        
        // Start server if not running
        if ($serverDetails['state'] !== 'running') {
            echo "Starting server...\n";
            $server->startServer($serverUuid);
            
            // Wait for server to start
            $timeout = 60; // 1 minute timeout
            $startTime = time();
            
            while ((time() - $startTime) < $timeout) {
                $status = $server->getServerStatus($serverUuid);
                if ($status['state'] === 'running') {
                    echo "✅ Server started successfully!\n";
                    break;
                }
                sleep(5);
            }
        }
        
        // Monitor server resources
        echo "Monitoring server resources...\n";
        $resources = $server->getServerResources($serverUuid);
        echo "CPU Usage: {$resources['cpu']}%\n";
        echo "Memory Usage: {$resources['memory']} MB\n";
        echo "Disk Usage: {$resources['disk']} MB\n";
        
        // Send a test command
        echo "Sending test command...\n";
        $server->sendCommand($serverUuid, 'say Hello from Wings API!');
        
    } catch (WingsException $e) {
        echo "❌ Error managing server: " . $e->getMessage() . "\n";
    }
}

// Usage
manageServerLifecycle($wings, 'server-uuid-here');
```

### Bulk Server Operations

```php
<?php
function bulkServerOperations($wings, $serverUuids, $operation) {
    $server = $wings->getServer();
    $results = [];
    
    echo "Performing bulk $operation operation on " . count($serverUuids) . " servers...\n";
    
    foreach ($serverUuids as $serverUuid) {
        try {
            switch ($operation) {
                case 'start':
                    $server->startServer($serverUuid);
                    break;
                case 'stop':
                    $server->stopServer($serverUuid);
                    break;
                case 'restart':
                    $server->restartServer($serverUuid);
                    break;
                case 'kill':
                    $server->killServer($serverUuid);
                    break;
            }
            
            $results[$serverUuid] = 'success';
            echo "✅ Server $serverUuid: $operation successful\n";
            
        } catch (WingsException $e) {
            $results[$serverUuid] = 'error: ' . $e->getMessage();
            echo "❌ Server $serverUuid: $operation failed - " . $e->getMessage() . "\n";
        }
    }
    
    return $results;
}

// Usage
$serverUuids = ['server1-uuid', 'server2-uuid', 'server3-uuid'];
$results = bulkServerOperations($wings, $serverUuids, 'restart');

// Summary
$successCount = count(array_filter($results, fn($result) => $result === 'success'));
echo "\nSummary: $successCount/" . count($serverUuids) . " servers processed successfully\n";
```

## Docker Management Examples

### Container Health Check

```php
<?php
function checkContainerHealth($wings) {
    $docker = $wings->getDocker();
    
    try {
        $containers = $docker->listContainers();
        echo "🐳 Container Health Check\n";
        echo "========================\n\n";
        
        foreach ($containers as $container) {
            $containerId = $container['id'];
            $status = $container['status'];
            
            echo "Container: " . substr($containerId, 0, 12) . "...\n";
            echo "Image: {$container['image']}\n";
            echo "Status: $status\n";
            
            if ($status === 'running') {
                try {
                    $stats = $docker->getContainerStats($containerId);
                    echo "CPU: {$stats['cpu']}% | Memory: {$stats['memory']} MB\n";
                    
                    // Check for high resource usage
                    if ($stats['cpu'] > 80) {
                        echo "⚠️ High CPU usage detected\n";
                    }
                    if ($stats['memory'] > 1024) { // 1GB
                        echo "⚠️ High memory usage detected\n";
                    }
                } catch (WingsException $e) {
                    echo "❌ Error getting stats: " . $e->getMessage() . "\n";
                }
            } else {
                echo "⚠️ Container is not running\n";
            }
            
            echo "---\n";
        }
        
    } catch (WingsException $e) {
        echo "❌ Error checking container health: " . $e->getMessage() . "\n";
    }
}

// Usage
checkContainerHealth($wings);
```

### Docker Image Management

```php
<?php
function manageDockerImages($wings) {
    $docker = $wings->getDocker();
    
    try {
        // List all images
        $images = $docker->getImages();
        echo "🖼️ Docker Images (" . count($images) . ")\n";
        echo "========================\n\n";
        
        $totalSize = 0;
        foreach ($images as $image) {
            $size = $image['size'];
            $totalSize += $size;
            
            echo "Image: {$image['repository']}:{$image['tag']}\n";
            echo "ID: {$image['id']}\n";
            echo "Size: " . number_format($size, 2) . " MB\n";
            echo "Created: {$image['created']}\n";
            echo "---\n";
        }
        
        echo "Total Images: " . count($images) . "\n";
        echo "Total Size: " . number_format($totalSize, 2) . " MB\n";
        
        // Prune unused images if total size is too large
        if ($totalSize > 10000) { // 10GB
            echo "\n🧹 Pruning unused images...\n";
            $pruneResult = $docker->pruneImages();
            echo "Pruned {$pruneResult['deleted']} images\n";
            echo "Freed " . number_format($pruneResult['space_reclaimed'], 2) . " MB\n";
        }
        
    } catch (WingsException $e) {
        echo "❌ Error managing Docker images: " . $e->getMessage() . "\n";
    }
}

// Usage
manageDockerImages($wings);
```

## Transfer Management Examples

### Server Transfer with Progress

```php
<?php
function transferServerWithProgress($wings, $serverUuid, $targetNode, $targetUrl, $targetToken) {
    $transfer = $wings->getTransfer();
    $server = $wings->getServer();
    
    try {
        // Ensure server is stopped
        $serverDetails = $server->getServer($serverUuid);
        if ($serverDetails['state'] !== 'offline') {
            echo "Stopping server before transfer...\n";
            $server->stopServer($serverUuid);
            
            // Wait for server to stop
            $timeout = 60;
            $startTime = time();
            while ((time() - $startTime) < $timeout) {
                $status = $server->getServerStatus($serverUuid);
                if ($status['state'] === 'offline') {
                    break;
                }
                sleep(5);
            }
        }
        
        // Start transfer
        $transferData = [
            'server' => $serverUuid,
            'node' => $targetNode,
            'url' => $targetUrl,
            'token' => $targetToken
        ];
        
        $transferResult = $transfer->startTransfer($transferData);
        $transferId = $transferResult['id'];
        
        echo "Transfer started with ID: $transferId\n";
        echo "Monitoring progress...\n";
        
        // Monitor transfer progress
        $lastProgress = 0;
        $timeout = 3600; // 1 hour timeout
        $startTime = time();
        
        while ((time() - $startTime) < $timeout) {
            $status = $transfer->getTransferStatus($transferId);
            $currentProgress = $status['progress'];
            
            if ($currentProgress > $lastProgress) {
                echo "Progress: {$currentProgress}% - {$status['status']}\n";
                $lastProgress = $currentProgress;
            }
            
            if ($status['status'] === 'completed') {
                echo "✅ Transfer completed successfully!\n";
                return true;
            } elseif ($status['status'] === 'failed') {
                echo "❌ Transfer failed!\n";
                return false;
            } elseif ($status['status'] === 'cancelled') {
                echo "⚠️ Transfer was cancelled\n";
                return false;
            }
            
            sleep(10); // Check every 10 seconds
        }
        
        echo "⏰ Transfer timed out\n";
        return false;
        
    } catch (WingsException $e) {
        echo "❌ Error during transfer: " . $e->getMessage() . "\n";
        return false;
    }
}

// Usage
$success = transferServerWithProgress(
    $wings,
    'server-uuid-here',
    2, // Target node ID
    'https://target-wings.example.com',
    'target-node-token-id.target-node-token-secret'
);
```

## JWT Token Examples

### Secure File Access

```php
<?php
function secureFileAccess($wings, $serverUuid, $filePath) {
    $jwt = $wings->getJwt();
    
    try {
        // Generate file token
        $token = $jwt->generateFileToken($serverUuid, $filePath);
        
        // Construct secure URL
        $baseUrl = $wings->getBaseUrl();
        $secureUrl = "{$baseUrl}/api/servers/{$serverUuid}/files/download?token={$token}";
        
        echo "🔐 Secure File Access\n";
        echo "====================\n";
        echo "Server: $serverUuid\n";
        echo "File: $filePath\n";
        echo "Token: " . substr($token, 0, 20) . "...\n";
        echo "Secure URL: $secureUrl\n";
        
        return [
            'success' => true,
            'token' => $token,
            'url' => $secureUrl
        ];
        
    } catch (WingsException $e) {
        echo "❌ Error generating secure file access: " . $e->getMessage() . "\n";
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Usage
$fileAccess = secureFileAccess($wings, 'server-uuid-here', '/path/to/file.txt');
```

### Token Management System

```php
<?php
class WingsTokenManager {
    private $jwt;
    private $tokens = [];
    
    public function __construct($jwt) {
        $this->jwt = $jwt;
    }
    
    public function getServerToken($serverUuid): string {
        $key = "server_{$serverUuid}";
        
        // Check if we have a valid cached token
        if (isset($this->tokens[$key])) {
            $token = $this->tokens[$key];
            if (!$this->isTokenExpired($token)) {
                return $token;
            }
        }
        
        // Generate new token
        $token = $this->jwt->generateServerToken($serverUuid);
        $this->tokens[$key] = $token;
        
        return $token;
    }
    
    public function getFileToken($serverUuid, $filePath): string {
        $key = "file_{$serverUuid}_{$filePath}";
        
        // Check if we have a valid cached token
        if (isset($this->tokens[$key])) {
            $token = $this->tokens[$key];
            if (!$this->isTokenExpired($token)) {
                return $token;
            }
        }
        
        // Generate new token
        $token = $this->jwt->generateFileToken($serverUuid, $filePath);
        $this->tokens[$key] = $token;
        
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
    
    public function getTokenCount(): int {
        return count($this->tokens);
    }
}

// Usage
$tokenManager = new WingsTokenManager($wings->getJwt());
$serverToken = $tokenManager->getServerToken('server-uuid-here');
$fileToken = $tokenManager->getFileToken('server-uuid-here', '/path/to/file.txt');

echo "Managed tokens: " . $tokenManager->getTokenCount() . "\n";
```

## Error Handling Examples

### Comprehensive Error Handler

```php
<?php
function handleWingsOperation($wings, $operation, $params = []) {
    try {
        switch ($operation) {
            case 'getSystemInfo':
                return $wings->getSystem()->getSystemInfo();
            case 'getServer':
                return $wings->getServer()->getServer($params['serverUuid']);
            case 'startServer':
                return $wings->getServer()->startServer($params['serverUuid']);
            case 'listContainers':
                return $wings->getDocker()->listContainers();
            default:
                throw new Exception("Unknown operation: $operation");
        }
    } catch (WingsAuthenticationException $e) {
        echo "🔐 Authentication Error: " . $e->getMessage() . "\n";
        echo "Please check your Wings token.\n";
        return null;
    } catch (WingsConnectionException $e) {
        echo "🌐 Connection Error: " . $e->getMessage() . "\n";
        echo "Please check if Wings daemon is running.\n";
        return null;
    } catch (WingsRequestException $e) {
        echo "📡 Request Error: " . $e->getMessage() . "\n";
        echo "Please check your parameters.\n";
        return null;
    } catch (WingsException $e) {
        echo "⚠️ Wings Error: " . $e->getMessage() . "\n";
        return null;
    } catch (Exception $e) {
        echo "❌ Unexpected Error: " . $e->getMessage() . "\n";
        return null;
    }
}

// Usage
$systemInfo = handleWingsOperation($wings, 'getSystemInfo');
if ($systemInfo) {
    echo "Wings Version: " . $systemInfo['version'] . "\n";
}

$server = handleWingsOperation($wings, 'getServer', ['serverUuid' => 'server-uuid-here']);
if ($server) {
    echo "Server Name: " . $server['name'] . "\n";
}
```

## Advanced Examples

### Wings Health Monitor

```php
<?php
function wingsHealthMonitor($wings) {
    $health = [
        'overall' => 'healthy',
        'checks' => [],
        'timestamp' => time()
    ];
    
    try {
        // Test connection
        if ($wings->testConnection()) {
            $health['checks']['connection'] = 'OK';
        } else {
            $health['checks']['connection'] = 'FAILED';
            $health['overall'] = 'unhealthy';
        }
        
        // Check system info
        $systemInfo = $wings->getSystem()->getSystemInfo();
        $health['checks']['wings_version'] = $systemInfo['version'];
        $health['checks']['docker_version'] = $systemInfo['docker_version'];
        
        // Check system resources
        $stats = $wings->getSystem()->getSystemStats();
        $health['checks']['cpu_usage'] = $stats['cpu'] . '%';
        $health['checks']['memory_usage'] = $stats['memory'] . '%';
        $health['checks']['disk_usage'] = $stats['disk'] . '%';
        
        // Check for high resource usage
        if ($stats['cpu'] > 80 || $stats['memory'] > 85 || $stats['disk'] > 90) {
            $health['overall'] = 'warning';
        }
        
    } catch (WingsException $e) {
        $health['overall'] = 'error';
        $health['checks']['error'] = $e->getMessage();
    }
    
    return $health;
}

// Usage
$health = wingsHealthMonitor($wings);
echo "Wings Health: " . strtoupper($health['overall']) . "\n";
foreach ($health['checks'] as $check => $value) {
    echo "  $check: $value\n";
}
```

### Automated Server Maintenance

```php
<?php
function automatedServerMaintenance($wings) {
    $server = $wings->getServer();
    $docker = $wings->getDocker();
    
    echo "🔧 Starting automated server maintenance...\n";
    
    try {
        // Get all servers
        $servers = $server->listServers();
        echo "Found " . count($servers) . " servers\n";
        
        foreach ($servers as $serverData) {
            $serverUuid = $serverData['uuid'];
            $serverName = $serverData['name'];
            
            echo "\nProcessing server: $serverName ($serverUuid)\n";
            
            // Check server resources
            $resources = $server->getServerResources($serverUuid);
            echo "  CPU: {$resources['cpu']}% | Memory: {$resources['memory']} MB\n";
            
            // Restart server if high resource usage
            if ($resources['cpu'] > 90 || $resources['memory'] > 2048) {
                echo "  ⚠️ High resource usage detected, restarting server...\n";
                $server->restartServer($serverUuid);
                echo "  ✅ Server restart initiated\n";
            }
            
            // Clean up old logs if disk usage is high
            if ($resources['disk'] > 10240) { // 10GB
                echo "  🧹 High disk usage, cleaning up logs...\n";
                // Implementation would depend on your log cleanup strategy
            }
        }
        
        // Clean up unused Docker images
        echo "\n🧹 Cleaning up unused Docker images...\n";
        $pruneResult = $docker->pruneImages();
        echo "Pruned {$pruneResult['deleted']} images, freed " . number_format($pruneResult['space_reclaimed'], 2) . " MB\n";
        
        echo "\n✅ Automated maintenance completed successfully!\n";
        
    } catch (WingsException $e) {
        echo "❌ Error during maintenance: " . $e->getMessage() . "\n";
    }
}

// Usage
automatedServerMaintenance($wings);
```
