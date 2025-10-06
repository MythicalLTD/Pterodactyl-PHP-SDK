# Wings API - System Service

The System Service provides access to system information, statistics, and resource monitoring for your Wings daemon.

## Getting Started

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$wings = PterodactylSDK::wingsOnly('wings.example.com', 8080, 'https', 'token');
$system = $wings->getSystem();
```

## System Information

### Get System Info

```php
<?php
$info = $system->getSystemInfo();

echo "Wings Version: " . $info['version'] . "\n";
echo "Docker Version: " . $info['docker_version'] . "\n";
echo "System Time: " . $info['time'] . "\n";
echo "System Type: " . $info['type'] . "\n";
echo "System Architecture: " . $info['arch'] . "\n";
```

### Get System Statistics

```php
<?php
$stats = $system->getSystemStats();

echo "CPU Usage: " . $stats['cpu'] . "%\n";
echo "Memory Usage: " . $stats['memory'] . " MB\n";
echo "Disk Usage: " . $stats['disk'] . " MB\n";
echo "Network RX: " . $stats['network']['rx'] . " bytes\n";
echo "Network TX: " . $stats['network']['tx'] . " bytes\n";
```

### Get System Resources

```php
<?php
$resources = $system->getSystemResources();

echo "Total CPU Cores: " . $resources['cpu'] . "\n";
echo "Total Memory: " . $resources['memory'] . " MB\n";
echo "Total Disk: " . $resources['disk'] . " MB\n";
echo "Available Memory: " . $resources['available_memory'] . " MB\n";
echo "Available Disk: " . $resources['available_disk'] . " MB\n";
```

## Resource Monitoring

### Real-time Monitoring

```php
<?php
function monitorSystemResources($system, $duration = 300) {
    $startTime = time();
    $data = [];
    
    while ((time() - $startTime) < $duration) {
        try {
            $stats = $system->getSystemStats();
            $data[] = [
                'timestamp' => time(),
                'cpu' => $stats['cpu'],
                'memory' => $stats['memory'],
                'disk' => $stats['disk'],
                'network' => $stats['network']
            ];
            
            sleep(10); // Check every 10 seconds
        } catch (Exception $e) {
            echo "Error getting stats: " . $e->getMessage() . "\n";
            sleep(30); // Wait longer on error
        }
    }
    
    return $data;
}

// Monitor for 5 minutes
$monitoringData = monitorSystemResources($system, 300);
echo "Collected " . count($monitoringData) . " data points\n";
```

### Resource Alerts

```php
<?php
function checkResourceAlerts($system, $thresholds = []) {
    $defaultThresholds = [
        'cpu' => 80,
        'memory' => 85,
        'disk' => 90
    ];
    
    $thresholds = array_merge($defaultThresholds, $thresholds);
    $alerts = [];
    
    try {
        $stats = $system->getSystemStats();
        
        if ($stats['cpu'] > $thresholds['cpu']) {
            $alerts[] = "CPU usage high: {$stats['cpu']}% (threshold: {$thresholds['cpu']}%)";
        }
        
        if ($stats['memory'] > $thresholds['memory']) {
            $alerts[] = "Memory usage high: {$stats['memory']}% (threshold: {$thresholds['memory']}%)";
        }
        
        if ($stats['disk'] > $thresholds['disk']) {
            $alerts[] = "Disk usage high: {$stats['disk']}% (threshold: {$thresholds['disk']}%)";
        }
        
    } catch (Exception $e) {
        $alerts[] = "Error checking resources: " . $e->getMessage();
    }
    
    return $alerts;
}

// Check for alerts
$alerts = checkResourceAlerts($system);
if (!empty($alerts)) {
    echo "⚠️ Resource Alerts:\n";
    foreach ($alerts as $alert) {
        echo "- $alert\n";
    }
} else {
    echo "✅ All resources within normal limits\n";
}
```

## System Health Check

```php
<?php
function performSystemHealthCheck($system): array {
    $health = [
        'overall' => 'healthy',
        'checks' => [],
        'issues' => []
    ];
    
    try {
        // Check Wings version
        $info = $system->getSystemInfo();
        $health['checks']['wings_version'] = [
            'status' => 'ok',
            'value' => $info['version']
        ];
        
        // Check Docker version
        $health['checks']['docker_version'] = [
            'status' => 'ok',
            'value' => $info['docker_version']
        ];
        
        // Check resource usage
        $stats = $system->getSystemStats();
        $health['checks']['cpu_usage'] = [
            'status' => $stats['cpu'] > 90 ? 'warning' : 'ok',
            'value' => $stats['cpu'] . '%'
        ];
        
        $health['checks']['memory_usage'] = [
            'status' => $stats['memory'] > 90 ? 'warning' : 'ok',
            'value' => $stats['memory'] . '%'
        ];
        
        $health['checks']['disk_usage'] = [
            'status' => $stats['disk'] > 90 ? 'warning' : 'ok',
            'value' => $stats['disk'] . '%'
        ];
        
        // Determine overall health
        $warnings = array_filter($health['checks'], function($check) {
            return $check['status'] === 'warning';
        });
        
        if (!empty($warnings)) {
            $health['overall'] = 'warning';
            $health['issues'] = array_keys($warnings);
        }
        
    } catch (Exception $e) {
        $health['overall'] = 'error';
        $health['issues'][] = 'system_check_failed';
        $health['error'] = $e->getMessage();
    }
    
    return $health;
}

// Perform health check
$health = performSystemHealthCheck($system);
echo "System Health: " . strtoupper($health['overall']) . "\n";

foreach ($health['checks'] as $check => $data) {
    $status = $data['status'] === 'ok' ? '✅' : '⚠️';
    echo "$status $check: {$data['value']}\n";
}

if (!empty($health['issues'])) {
    echo "Issues: " . implode(', ', $health['issues']) . "\n";
}
```

## System Configuration

### Get System Configuration

```php
<?php
$config = $system->getSystemConfig();

echo "Debug Mode: " . ($config['debug'] ? 'Enabled' : 'Disabled') . "\n";
echo "UUID: " . $config['uuid'] . "\n";
echo "Token ID: " . $config['token_id'] . "\n";
echo "Sftp Port: " . $config['sftp']['port'] . "\n";
echo "Sftp Bind Address: " . $config['sftp']['bind_address'] . "\n";
```

### System Limits

```php
<?php
$limits = $system->getSystemLimits();

echo "Max CPU: " . $limits['cpu'] . "%\n";
echo "Max Memory: " . $limits['memory'] . " MB\n";
echo "Max Disk: " . $limits['disk'] . " MB\n";
echo "Max Network: " . $limits['network'] . " MB/s\n";
```

## Error Handling

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;

try {
    $info = $system->getSystemInfo();
    echo "System info retrieved successfully\n";
} catch (WingsRequestException $e) {
    echo "Request failed: " . $e->getMessage() . "\n";
} catch (WingsConnectionException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
```

## Best Practices

1. **Monitor Regularly** - Set up regular monitoring of system resources
2. **Set Alerts** - Configure alerts for high resource usage
3. **Health Checks** - Implement automated health checks
4. **Error Handling** - Always handle exceptions properly
5. **Caching** - Cache system info for performance (it doesn't change often)
6. **Logging** - Log system events for debugging

## Example: System Dashboard

```php
<?php
function generateSystemDashboard($system): string {
    $dashboard = "🖥️ Wings System Dashboard\n";
    $dashboard .= "========================\n\n";
    
    try {
        // System Info
        $info = $system->getSystemInfo();
        $dashboard .= "📊 System Information:\n";
        $dashboard .= "  Wings Version: {$info['version']}\n";
        $dashboard .= "  Docker Version: {$info['docker_version']}\n";
        $dashboard .= "  System Time: {$info['time']}\n\n";
        
        // Resource Usage
        $stats = $system->getSystemStats();
        $dashboard .= "📈 Resource Usage:\n";
        $dashboard .= "  CPU: {$stats['cpu']}%\n";
        $dashboard .= "  Memory: {$stats['memory']}%\n";
        $dashboard .= "  Disk: {$stats['disk']}%\n\n";
        
        // Health Status
        $health = performSystemHealthCheck($system);
        $status = $health['overall'] === 'healthy' ? '✅' : '⚠️';
        $dashboard .= "🏥 Health Status: $status " . strtoupper($health['overall']) . "\n";
        
    } catch (Exception $e) {
        $dashboard .= "❌ Error generating dashboard: " . $e->getMessage() . "\n";
    }
    
    return $dashboard;
}

// Generate and display dashboard
echo generateSystemDashboard($system);
```
