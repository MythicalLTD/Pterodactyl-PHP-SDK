# Wings API - Docker Service

The Docker Service provides access to Docker container and image management operations on the Wings daemon.

## Getting Started

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$wings = PterodactylSDK::wingsOnly('wings.example.com', 8080, 'https', 'token');
$docker = $wings->getDocker();
```

## Container Management

### List All Containers

```php
<?php
$containers = $docker->listContainers();

foreach ($containers as $container) {
    echo "Container ID: " . $container['id'] . "\n";
    echo "Image: " . $container['image'] . "\n";
    echo "Status: " . $container['status'] . "\n";
    echo "Created: " . $container['created'] . "\n";
    echo "---\n";
}
```

### Get Container Details

```php
<?php
$containerId = 'container-id-here';
$container = $docker->getContainer($containerId);

echo "Container ID: " . $container['id'] . "\n";
echo "Image: " . $container['image'] . "\n";
echo "Status: " . $container['status'] . "\n";
echo "Created: " . $container['created'] . "\n";
echo "Started: " . $container['started'] . "\n";
echo "Finished: " . $container['finished'] . "\n";
```

### Get Container Logs

```php
<?php
$containerId = 'container-id-here';
$logs = $docker->getContainerLogs($containerId, 100); // Get last 100 lines

foreach ($logs as $log) {
    echo $log . "\n";
}
```

### Get Container Stats

```php
<?php
$containerId = 'container-id-here';
$stats = $docker->getContainerStats($containerId);

echo "CPU Usage: " . $stats['cpu'] . "%\n";
echo "Memory Usage: " . $stats['memory'] . " MB\n";
echo "Network RX: " . $stats['network']['rx'] . " bytes\n";
echo "Network TX: " . $stats['network']['tx'] . " bytes\n";
echo "Block I/O Read: " . $stats['block_io']['read'] . " bytes\n";
echo "Block I/O Write: " . $stats['block_io']['write'] . " bytes\n";
```

## Container Operations

### Start Container

```php
<?php
$containerId = 'container-id-here';
$docker->startContainer($containerId);
echo "Container started successfully!";
```

### Stop Container

```php
<?php
$containerId = 'container-id-here';
$docker->stopContainer($containerId);
echo "Container stopped successfully!";
```

### Restart Container

```php
<?php
$containerId = 'container-id-here';
$docker->restartContainer($containerId);
echo "Container restarted successfully!";
```

### Kill Container

```php
<?php
$containerId = 'container-id-here';
$docker->killContainer($containerId);
echo "Container killed successfully!";
```

### Remove Container

```php
<?php
$containerId = 'container-id-here';
$docker->removeContainer($containerId);
echo "Container removed successfully!";
```

## Image Management

### List Docker Images

```php
<?php
$images = $docker->getImages();

foreach ($images as $image) {
    echo "Repository: " . $image['repository'] . "\n";
    echo "Tag: " . $image['tag'] . "\n";
    echo "Image ID: " . $image['id'] . "\n";
    echo "Size: " . $image['size'] . " MB\n";
    echo "Created: " . $image['created'] . "\n";
    echo "---\n";
}
```

### Get Image Details

```php
<?php
$imageId = 'image-id-here';
$image = $docker->getImage($imageId);

echo "Image ID: " . $image['id'] . "\n";
echo "Repository: " . $image['repository'] . "\n";
echo "Tag: " . $image['tag'] . "\n";
echo "Size: " . $image['size'] . " MB\n";
echo "Created: " . $image['created'] . "\n";
echo "Architecture: " . $image['architecture'] . "\n";
echo "OS: " . $image['os'] . "\n";
```

### Pull Docker Image

```php
<?php
$imageName = 'quay.io/pterodactyl/core:java';
$docker->pullImage($imageName);
echo "Image pulled successfully!";
```

### Remove Docker Image

```php
<?php
$imageId = 'image-id-here';
$docker->removeImage($imageId);
echo "Image removed successfully!";
```

### Prune Unused Images

```php
<?php
$result = $docker->pruneImages();
echo "Pruned " . $result['deleted'] . " images\n";
echo "Freed " . $result['space_reclaimed'] . " MB\n";
```

## Container Creation

### Create Container

```php
<?php
$containerConfig = [
    'name' => 'my-server-container',
    'image' => 'quay.io/pterodactyl/core:java',
    'env' => [
        'SERVER_JARFILE' => 'server.jar',
        'SERVER_MEMORY' => '1024'
    ],
    'ports' => [
        '25565/tcp' => [
            'HostPort' => '25565'
        ]
    ],
    'volumes' => [
        '/var/lib/pterodactyl/volumes/server-uuid:/home/container'
    ],
    'resources' => [
        'memory' => 1024 * 1024 * 1024, // 1GB in bytes
        'cpu' => 100 // 100% of one CPU core
    ]
];

$container = $docker->createContainer($containerConfig);
echo "Container created with ID: " . $container['id'];
```

## Monitoring and Health Checks

### Container Health Check

```php
<?php
function checkContainerHealth($docker, $containerId): array {
    $health = [
        'status' => 'unknown',
        'details' => []
    ];
    
    try {
        $container = $docker->getContainer($containerId);
        $health['details']['status'] = $container['status'];
        
        if ($container['status'] === 'running') {
            $health['status'] = 'healthy';
        } elseif ($container['status'] === 'exited') {
            $health['status'] = 'stopped';
        } else {
            $health['status'] = 'unhealthy';
        }
        
        // Get resource usage
        $stats = $docker->getContainerStats($containerId);
        $health['details']['cpu'] = $stats['cpu'];
        $health['details']['memory'] = $stats['memory'];
        
    } catch (Exception $e) {
        $health['status'] = 'error';
        $health['details']['error'] = $e->getMessage();
    }
    
    return $health;
}

// Usage
$health = checkContainerHealth($docker, $containerId);
echo "Container Health: " . strtoupper($health['status']) . "\n";
```

### Monitor Container Resources

```php
<?php
function monitorContainerResources($docker, $containerId, $duration = 300) {
    $startTime = time();
    $data = [];
    
    while ((time() - $startTime) < $duration) {
        try {
            $stats = $docker->getContainerStats($containerId);
            $data[] = [
                'timestamp' => time(),
                'cpu' => $stats['cpu'],
                'memory' => $stats['memory'],
                'network' => $stats['network'],
                'block_io' => $stats['block_io']
            ];
            
            sleep(10); // Check every 10 seconds
        } catch (Exception $e) {
            echo "Error getting stats: " . $e->getMessage() . "\n";
            sleep(30);
        }
    }
    
    return $data;
}

// Monitor container for 5 minutes
$monitoringData = monitorContainerResources($docker, $containerId, 300);
echo "Collected " . count($monitoringData) . " data points\n";
```

## Bulk Operations

### Bulk Container Operations

```php
<?php
function bulkContainerOperation($docker, $containerIds, $operation) {
    $results = [];
    
    foreach ($containerIds as $containerId) {
        try {
            switch ($operation) {
                case 'start':
                    $docker->startContainer($containerId);
                    break;
                case 'stop':
                    $docker->stopContainer($containerId);
                    break;
                case 'restart':
                    $docker->restartContainer($containerId);
                    break;
                case 'kill':
                    $docker->killContainer($containerId);
                    break;
                case 'remove':
                    $docker->removeContainer($containerId);
                    break;
            }
            $results[$containerId] = 'success';
        } catch (Exception $e) {
            $results[$containerId] = 'error: ' . $e->getMessage();
        }
    }
    
    return $results;
}

// Usage
$containerIds = ['container1', 'container2', 'container3'];
$results = bulkContainerOperation($docker, $containerIds, 'restart');

foreach ($results as $containerId => $result) {
    echo "Container $containerId: $result\n";
}
```

## Error Handling

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;

try {
    $containers = $docker->listContainers();
    echo "Containers retrieved successfully\n";
} catch (WingsRequestException $e) {
    echo "Request failed: " . $e->getMessage() . "\n";
} catch (WingsConnectionException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
```

## Best Practices

1. **Monitor container resources** regularly
2. **Clean up unused images** periodically
3. **Use appropriate resource limits** for containers
4. **Handle exceptions properly** for all operations
5. **Implement health checks** for critical containers
6. **Log container operations** for debugging
7. **Use bulk operations** for efficiency

## Example: Docker Management Dashboard

```php
<?php
function generateDockerDashboard($docker): string {
    $dashboard = "🐳 Docker Management Dashboard\n";
    $dashboard .= "============================\n\n";
    
    try {
        // Container Information
        $containers = $docker->listContainers();
        $dashboard .= "📦 Containers (" . count($containers) . "):\n";
        
        foreach ($containers as $container) {
            $status = $container['status'] === 'running' ? '🟢' : '🔴';
            $dashboard .= "  $status {$container['id']} - {$container['image']}\n";
        }
        
        $dashboard .= "\n";
        
        // Image Information
        $images = $docker->getImages();
        $dashboard .= "🖼️ Images (" . count($images) . "):\n";
        
        foreach ($images as $image) {
            $dashboard .= "  📷 {$image['repository']}:{$image['tag']} ({$image['size']} MB)\n";
        }
        
    } catch (Exception $e) {
        $dashboard .= "❌ Error generating dashboard: " . $e->getMessage() . "\n";
    }
    
    return $dashboard;
}

// Generate and display dashboard
echo generateDockerDashboard($docker);
```
