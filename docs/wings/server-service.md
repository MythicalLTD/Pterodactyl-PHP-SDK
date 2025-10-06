# Wings API - Server Service

The Server Service provides direct access to server management operations on the Wings daemon, including creating, managing, and controlling game servers.

## Getting Started

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$wings = PterodactylSDK::wingsOnly('wings.example.com', 8080, 'https', 'token');
$server = $wings->getServer();
```

## Server Management

### List All Servers

```php
<?php
$servers = $server->listServers();

foreach ($servers as $serverData) {
    echo "Server UUID: " . $serverData['uuid'] . "\n";
    echo "Name: " . $serverData['name'] . "\n";
    echo "Status: " . $serverData['state'] . "\n";
    echo "Memory: " . $serverData['limits']['memory'] . " MB\n";
    echo "---\n";
}
```

### Get Server Details

```php
<?php
$serverUuid = 'server-uuid-here';
$serverDetails = $server->getServer($serverUuid);

echo "Server UUID: " . $serverDetails['uuid'] . "\n";
echo "Name: " . $serverDetails['name'] . "\n";
echo "Status: " . $serverDetails['state'] . "\n";
echo "Memory Limit: " . $serverDetails['limits']['memory'] . " MB\n";
echo "CPU Limit: " . $serverDetails['limits']['cpu'] . "%\n";
echo "Disk Limit: " . $serverDetails['limits']['disk'] . " MB\n";
echo "Docker Image: " . $serverDetails['docker_image'] . "\n";
```

### Create Server

```php
<?php
$serverData = [
    'uuid' => 'new-server-uuid',
    'name' => 'My New Server',
    'node' => 1,
    'sftp' => [
        'ip' => '0.0.0.0',
        'port' => 2022
    ],
    'limits' => [
        'memory' => 1024,
        'swap' => 0,
        'disk' => 10240,
        'io' => 500,
        'cpu' => 100
    ],
    'environment' => [
        'SERVER_JARFILE' => 'server.jar',
        'SERVER_MEMORY' => '1024',
        'BUILD_NUMBER' => 'latest'
    ],
    'startup' => 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}',
    'docker_image' => 'quay.io/pterodactyl/core:java',
    'allocations' => [
        'default' => [
            'ip' => '0.0.0.0',
            'port' => 25565
        ]
    ]
];

$newServer = $server->createServer($serverData);
echo "Server created successfully with UUID: " . $newServer['uuid'];
```

### Update Server

```php
<?php
$serverUuid = 'server-uuid-here';
$updateData = [
    'name' => 'Updated Server Name',
    'limits' => [
        'memory' => 2048,
        'swap' => 0,
        'disk' => 20480,
        'io' => 1000,
        'cpu' => 200
    ],
    'environment' => [
        'SERVER_MEMORY' => '2048'
    ]
];

$updatedServer = $server->updateServer($serverUuid, $updateData);
echo "Server updated successfully!";
```

### Delete Server

```php
<?php
$serverUuid = 'server-uuid-here';
$server->deleteServer($serverUuid);
echo "Server deleted successfully!";
```

## Power Commands

### Start Server

```php
<?php
$serverUuid = 'server-uuid-here';
$server->startServer($serverUuid);
echo "Server start command sent!";
```

### Stop Server

```php
<?php
$serverUuid = 'server-uuid-here';
$server->stopServer($serverUuid);
echo "Server stop command sent!";
```

### Restart Server

```php
<?php
$serverUuid = 'server-uuid-here';
$server->restartServer($serverUuid);
echo "Server restart command sent!";
```

### Kill Server

```php
<?php
$serverUuid = 'server-uuid-here';
$server->killServer($serverUuid);
echo "Server kill command sent!";
```

### Send Power Command

```php
<?php
$serverUuid = 'server-uuid-here';

// Available commands: start, stop, restart, kill
$server->sendPowerCommand($serverUuid, 'start');
echo "Power command sent!";
```

## Console Operations

### Send Console Command

```php
<?php
$serverUuid = 'server-uuid-here';
$command = 'say Hello World!';

$server->sendCommand($serverUuid, $command);
echo "Console command sent: $command";
```

### Get Server Logs

```php
<?php
$serverUuid = 'server-uuid-here';
$lines = 100; // Number of lines to retrieve

$logs = $server->getServerLogs($serverUuid, $lines);

foreach ($logs as $log) {
    echo $log . "\n";
}
```

### Get Server Console

```php
<?php
$serverUuid = 'server-uuid-here';
$console = $server->getServerConsole($serverUuid);

echo "Console output:\n";
echo $console;
```

## Resource Monitoring

### Get Server Resources

```php
<?php
$serverUuid = 'server-uuid-here';
$resources = $server->getServerResources($serverUuid);

echo "CPU Usage: " . $resources['cpu'] . "%\n";
echo "Memory Usage: " . $resources['memory'] . " MB\n";
echo "Disk Usage: " . $resources['disk'] . " MB\n";
echo "Network RX: " . $resources['network']['rx'] . " bytes\n";
echo "Network TX: " . $resources['network']['tx'] . " bytes\n";
```

### Monitor Server Resources

```php
<?php
function monitorServerResources($server, $serverUuid, $duration = 300) {
    $startTime = time();
    $data = [];
    
    while ((time() - $startTime) < $duration) {
        try {
            $resources = $server->getServerResources($serverUuid);
            $data[] = [
                'timestamp' => time(),
                'cpu' => $resources['cpu'],
                'memory' => $resources['memory'],
                'disk' => $resources['disk'],
                'network' => $resources['network']
            ];
            
            sleep(10); // Check every 10 seconds
        } catch (Exception $e) {
            echo "Error getting resources: " . $e->getMessage() . "\n";
            sleep(30); // Wait longer on error
        }
    }
    
    return $data;
}

// Monitor server for 5 minutes
$monitoringData = monitorServerResources($server, $serverUuid, 300);
echo "Collected " . count($monitoringData) . " data points\n";
```

## Server Status

### Get Server Status

```php
<?php
$serverUuid = 'server-uuid-here';
$status = $server->getServerStatus($serverUuid);

echo "Server Status: " . $status['state'] . "\n";
echo "Is Running: " . ($status['is_running'] ? 'Yes' : 'No') . "\n";
echo "Uptime: " . $status['uptime'] . " seconds\n";
echo "PID: " . ($status['pid'] ?? 'N/A') . "\n";
```

### Wait for Server State

```php
<?php
function waitForServerState($server, $serverUuid, $targetState, $timeout = 300) {
    $startTime = time();
    
    while ((time() - $startTime) < $timeout) {
        try {
            $status = $server->getServerStatus($serverUuid);
            
            if ($status['state'] === $targetState) {
                return true;
            }
            
            sleep(5); // Check every 5 seconds
        } catch (Exception $e) {
            echo "Error checking status: " . $e->getMessage() . "\n";
            sleep(10);
        }
    }
    
    return false;
}

// Wait for server to start
if (waitForServerState($server, $serverUuid, 'running', 300)) {
    echo "Server is now running!\n";
} else {
    echo "Server failed to start within timeout\n";
}
```

## Bulk Operations

### Bulk Start Servers

```php
<?php
function bulkStartServers($server, $serverUuids) {
    $results = [];
    
    foreach ($serverUuids as $serverUuid) {
        try {
            $server->startServer($serverUuid);
            $results[$serverUuid] = 'success';
        } catch (Exception $e) {
            $results[$serverUuid] = 'error: ' . $e->getMessage();
        }
    }
    
    return $results;
}

$serverUuids = ['server1-uuid', 'server2-uuid', 'server3-uuid'];
$results = bulkStartServers($server, $serverUuids);

foreach ($results as $serverUuid => $result) {
    echo "Server $serverUuid: $result\n";
}
```

### Bulk Stop Servers

```php
<?php
function bulkStopServers($server, $serverUuids) {
    $results = [];
    
    foreach ($serverUuids as $serverUuid) {
        try {
            $server->stopServer($serverUuid);
            $results[$serverUuid] = 'success';
        } catch (Exception $e) {
            $results[$serverUuid] = 'error: ' . $e->getMessage();
        }
    }
    
    return $results;
}

$serverUuids = ['server1-uuid', 'server2-uuid', 'server3-uuid'];
$results = bulkStopServers($server, $serverUuids);

foreach ($results as $serverUuid => $result) {
    echo "Server $serverUuid: $result\n";
}
```

## Server Configuration

### Get Server Configuration

```php
<?php
$serverUuid = 'server-uuid-here';
$config = $server->getServerConfig($serverUuid);

echo "Server Name: " . $config['name'] . "\n";
echo "Docker Image: " . $config['docker_image'] . "\n";
echo "Startup Command: " . $config['startup'] . "\n";
echo "Memory Limit: " . $config['limits']['memory'] . " MB\n";
echo "CPU Limit: " . $config['limits']['cpu'] . "%\n";
```

### Update Server Environment

```php
<?php
$serverUuid = 'server-uuid-here';
$environment = [
    'SERVER_MEMORY' => '2048',
    'SERVER_JARFILE' => 'server.jar',
    'BUILD_NUMBER' => 'latest',
    'EULA' => 'TRUE'
];

$server->updateServerEnvironment($serverUuid, $environment);
echo "Server environment updated!";
```

## Error Handling

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;

try {
    $serverDetails = $server->getServer($serverUuid);
    echo "Server details retrieved successfully\n";
} catch (WingsRequestException $e) {
    echo "Request failed: " . $e->getMessage() . "\n";
} catch (WingsConnectionException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
```

## Best Practices

1. **Always check server status** before performing operations
2. **Handle exceptions properly** for all server operations
3. **Monitor server resources** regularly
4. **Use appropriate timeouts** for long-running operations
5. **Validate server UUIDs** before operations
6. **Implement retry logic** for transient failures
7. **Log server operations** for debugging

## Example: Server Management Dashboard

```php
<?php
function generateServerDashboard($server): string {
    $dashboard = "🎮 Server Management Dashboard\n";
    $dashboard .= "=============================\n\n";
    
    try {
        $servers = $server->listServers();
        
        foreach ($servers as $serverData) {
            $serverUuid = $serverData['uuid'];
            $dashboard .= "📊 Server: {$serverData['name']} ({$serverUuid})\n";
            $dashboard .= "  Status: {$serverData['state']}\n";
            $dashboard .= "  Memory: {$serverData['limits']['memory']} MB\n";
            
            // Get real-time resources
            try {
                $resources = $server->getServerResources($serverUuid);
                $dashboard .= "  CPU Usage: {$resources['cpu']}%\n";
                $dashboard .= "  Memory Usage: {$resources['memory']} MB\n";
            } catch (Exception $e) {
                $dashboard .= "  Resource data unavailable\n";
            }
            
            $dashboard .= "\n";
        }
        
    } catch (Exception $e) {
        $dashboard .= "❌ Error generating dashboard: " . $e->getMessage() . "\n";
    }
    
    return $dashboard;
}

// Generate and display dashboard
echo generateServerDashboard($server);
```
