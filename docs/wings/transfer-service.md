# Wings API - Transfer Service

The Transfer Service provides functionality for transferring servers between Wings nodes in your Pterodactyl installation.

## Getting Started

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$wings = PterodactylSDK::wingsOnly('wings.example.com', 8080, 'https', 'token');
$transfer = $wings->getTransfer();
```

## Server Transfers

### Start Server Transfer

```php
<?php
$transferData = [
    'server' => 'server-uuid-here',
    'node' => 2,  // Target node ID
    'url' => 'https://target-wings.example.com',
    'token' => 'target-node-token-id.target-node-token-secret'
];

$transfer = $transfer->startTransfer($transferData);
echo "Transfer started with ID: " . $transfer['id'];
```

### Get Transfer Status

```php
<?php
$transferId = 'transfer-id-here';
$status = $transfer->getTransferStatus($transferId);

echo "Transfer ID: " . $status['id'] . "\n";
echo "Status: " . $status['status'] . "\n";
echo "Progress: " . $status['progress'] . "%\n";
echo "Server: " . $status['server'] . "\n";
echo "Source Node: " . $status['source_node'] . "\n";
echo "Target Node: " . $status['target_node'] . "\n";
echo "Started: " . $status['started_at'] . "\n";
echo "Updated: " . $status['updated_at'] . "\n";
```

### Cancel Transfer

```php
<?php
$transferId = 'transfer-id-here';
$transfer->cancelTransfer($transferId);
echo "Transfer cancelled successfully!";
```

### List Active Transfers

```php
<?php
$transfers = $transfer->listTransfers();

foreach ($transfers as $transferData) {
    echo "Transfer ID: " . $transferData['id'] . "\n";
    echo "Server: " . $transferData['server'] . "\n";
    echo "Status: " . $transferData['status'] . "\n";
    echo "Progress: " . $transferData['progress'] . "%\n";
    echo "---\n";
}
```

## Transfer Monitoring

### Monitor Transfer Progress

```php
<?php
function monitorTransfer($transfer, $transferId, $timeout = 3600) {
    $startTime = time();
    $lastProgress = 0;
    
    while ((time() - $startTime) < $timeout) {
        try {
            $status = $transfer->getTransferStatus($transferId);
            $currentProgress = $status['progress'];
            
            if ($currentProgress > $lastProgress) {
                echo "Transfer progress: {$currentProgress}%\n";
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
        } catch (Exception $e) {
            echo "Error checking transfer status: " . $e->getMessage() . "\n";
            sleep(30);
        }
    }
    
    echo "⏰ Transfer monitoring timed out\n";
    return false;
}

// Monitor transfer
$success = monitorTransfer($transfer, $transferId, 3600); // 1 hour timeout
```

### Transfer with Progress Callback

```php
<?php
function transferWithCallback($transfer, $transferData, $progressCallback = null) {
    // Start transfer
    $transferResult = $transfer->startTransfer($transferData);
    $transferId = $transferResult['id'];
    
    echo "Transfer started with ID: $transferId\n";
    
    // Monitor progress
    $startTime = time();
    $timeout = 3600; // 1 hour
    $lastProgress = 0;
    
    while ((time() - $startTime) < $timeout) {
        try {
            $status = $transfer->getTransferStatus($transferId);
            $currentProgress = $status['progress'];
            
            if ($currentProgress > $lastProgress) {
                if ($progressCallback) {
                    $progressCallback($currentProgress, $status);
                } else {
                    echo "Progress: {$currentProgress}%\n";
                }
                $lastProgress = $currentProgress;
            }
            
            if ($status['status'] === 'completed') {
                return ['success' => true, 'transfer_id' => $transferId];
            } elseif ($status['status'] === 'failed') {
                return ['success' => false, 'error' => 'Transfer failed', 'transfer_id' => $transferId];
            } elseif ($status['status'] === 'cancelled') {
                return ['success' => false, 'error' => 'Transfer cancelled', 'transfer_id' => $transferId];
            }
            
            sleep(10);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'transfer_id' => $transferId];
        }
    }
    
    return ['success' => false, 'error' => 'Transfer timed out', 'transfer_id' => $transferId];
}

// Usage with callback
$result = transferWithCallback($transfer, $transferData, function($progress, $status) {
    echo "🔄 Transfer progress: {$progress}% - {$status['status']}\n";
});

if ($result['success']) {
    echo "✅ Transfer completed successfully!\n";
} else {
    echo "❌ Transfer failed: " . $result['error'] . "\n";
}
```

## Bulk Transfers

### Transfer Multiple Servers

```php
<?php
function bulkTransferServers($transfer, $servers, $targetNode, $targetUrl, $targetToken) {
    $results = [];
    
    foreach ($servers as $serverUuid) {
        $transferData = [
            'server' => $serverUuid,
            'node' => $targetNode,
            'url' => $targetUrl,
            'token' => $targetToken
        ];
        
        try {
            $transferResult = $transfer->startTransfer($transferData);
            $results[$serverUuid] = [
                'success' => true,
                'transfer_id' => $transferResult['id']
            ];
            echo "Started transfer for server $serverUuid\n";
        } catch (Exception $e) {
            $results[$serverUuid] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
            echo "Failed to start transfer for server $serverUuid: " . $e->getMessage() . "\n";
        }
    }
    
    return $results;
}

// Usage
$servers = ['server1-uuid', 'server2-uuid', 'server3-uuid'];
$results = bulkTransferServers(
    $transfer,
    $servers,
    2, // Target node ID
    'https://target-wings.example.com',
    'target-node-token-id.target-node-token-secret'
);

foreach ($results as $serverUuid => $result) {
    if ($result['success']) {
        echo "✅ Server $serverUuid: Transfer started (ID: {$result['transfer_id']})\n";
    } else {
        echo "❌ Server $serverUuid: {$result['error']}\n";
    }
}
```

## Transfer Validation

### Validate Transfer Requirements

```php
<?php
function validateTransferRequirements($server, $targetNode, $targetUrl, $targetToken): array {
    $validation = [
        'valid' => true,
        'errors' => []
    ];
    
    try {
        // Check if server exists and is stopped
        $serverDetails = $server->getServer($server);
        if ($serverDetails['state'] !== 'offline') {
            $validation['valid'] = false;
            $validation['errors'][] = 'Server must be stopped before transfer';
        }
        
        // Validate target URL format
        if (!filter_var($targetUrl, FILTER_VALIDATE_URL)) {
            $validation['valid'] = false;
            $validation['errors'][] = 'Invalid target URL format';
        }
        
        // Validate token format
        if (strpos($targetToken, '.') === false) {
            $validation['valid'] = false;
            $validation['errors'][] = 'Invalid target token format (should be node-id.node-secret)';
        }
        
    } catch (Exception $e) {
        $validation['valid'] = false;
        $validation['errors'][] = 'Server validation failed: ' . $e->getMessage();
    }
    
    return $validation;
}

// Usage
$validation = validateTransferRequirements(
    'server-uuid',
    2,
    'https://target-wings.example.com',
    'node-token-id.node-token-secret'
);

if ($validation['valid']) {
    echo "✅ Transfer requirements validated\n";
} else {
    echo "❌ Transfer validation failed:\n";
    foreach ($validation['errors'] as $error) {
        echo "  - $error\n";
    }
}
```

## Transfer History

### Get Transfer History

```php
<?php
function getTransferHistory($transfer, $serverUuid = null, $limit = 50): array {
    $transfers = $transfer->listTransfers();
    $history = [];
    
    foreach ($transfers as $transferData) {
        if ($serverUuid && $transferData['server'] !== $serverUuid) {
            continue;
        }
        
        $history[] = [
            'id' => $transferData['id'],
            'server' => $transferData['server'],
            'status' => $transferData['status'],
            'progress' => $transferData['progress'],
            'started_at' => $transferData['started_at'],
            'updated_at' => $transferData['updated_at']
        ];
    }
    
    // Sort by started_at descending
    usort($history, function($a, $b) {
        return strtotime($b['started_at']) - strtotime($a['started_at']);
    });
    
    return array_slice($history, 0, $limit);
}

// Usage
$history = getTransferHistory($transfer, 'server-uuid', 10);
foreach ($history as $transfer) {
    echo "Transfer {$transfer['id']}: {$transfer['status']} ({$transfer['progress']}%)\n";
}
```

## Error Handling

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;

try {
    $transfer = $transfer->startTransfer($transferData);
    echo "Transfer started successfully\n";
} catch (WingsRequestException $e) {
    echo "Request failed: " . $e->getMessage() . "\n";
} catch (WingsConnectionException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
```

## Best Practices

1. **Stop servers before transfer** - Ensure servers are offline
2. **Validate requirements** - Check server status and target availability
3. **Monitor progress** - Track transfer status and handle failures
4. **Handle timeouts** - Set appropriate timeouts for large transfers
5. **Clean up failed transfers** - Cancel stuck or failed transfers
6. **Log transfer events** - Keep records of transfer operations
7. **Test connectivity** - Verify target Wings daemon is accessible

## Example: Transfer Management Dashboard

```php
<?php
function generateTransferDashboard($transfer): string {
    $dashboard = "🔄 Transfer Management Dashboard\n";
    $dashboard .= "==============================\n\n";
    
    try {
        $transfers = $transfer->listTransfers();
        
        if (empty($transfers)) {
            $dashboard .= "📭 No active transfers\n";
        } else {
            $dashboard .= "📊 Active Transfers (" . count($transfers) . "):\n";
            
            foreach ($transfers as $transferData) {
                $status = $transferData['status'];
                $icon = match($status) {
                    'completed' => '✅',
                    'failed' => '❌',
                    'cancelled' => '⚠️',
                    'in_progress' => '🔄',
                    default => '⏳'
                };
                
                $dashboard .= "  $icon {$transferData['id']} - {$transferData['server']} ({$transferData['progress']}%)\n";
            }
        }
        
    } catch (Exception $e) {
        $dashboard .= "❌ Error generating dashboard: " . $e->getMessage() . "\n";
    }
    
    return $dashboard;
}

// Generate and display dashboard
echo generateTransferDashboard($transfer);
```
