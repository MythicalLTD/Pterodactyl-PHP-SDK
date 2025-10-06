# Examples

This page contains comprehensive examples of common use cases with the Pterodactyl PHP SDK.

## Basic Setup

```php
<?php
require_once 'vendor/autoload.php';

use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PterodactylException;

// Initialize SDK
$sdk = new PterodactylSDK(
    'https://panel.example.com',
    'ptlc_admin_xxxxxxxxxxxxx',
    'ptlc_client_xxxxxxxxxxxxx'
);
```

## Server Management Examples

### List All Servers with Pagination

```php
<?php
function listAllServers($sdk) {
    $page = 1;
    $allServers = [];
    
    do {
        $response = $sdk->admin()->servers()->listServers($page, 50);
        $allServers = array_merge($allServers, $response['data']);
        $page++;
    } while ($response['meta']['pagination']['current_page'] < $response['meta']['pagination']['total_pages']);
    
    return $allServers;
}

$servers = listAllServers($sdk);
echo "Total servers: " . count($servers) . "\n";
```

### Create a Minecraft Server

```php
<?php
function createMinecraftServer($sdk, $userId, $nodeId) {
    $serverData = [
        'name' => 'My Minecraft Server',
        'user' => $userId,
        'node' => $nodeId,
        'nest' => 1, // Minecraft nest
        'egg' => 1,  // Minecraft egg
        'docker_image' => 'quay.io/pterodactyl/core:java',
        'startup' => 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}',
        'environment' => [
            'SERVER_JARFILE' => 'server.jar',
            'SERVER_MEMORY' => '1024',
            'BUILD_NUMBER' => 'latest'
        ],
        'limits' => [
            'memory' => 1024,
            'swap' => 0,
            'disk' => 10240,
            'io' => 500,
            'cpu' => 100
        ],
        'feature_limits' => [
            'databases' => 1,
            'allocations' => 1,
            'backups' => 1
        ],
        'allocation' => [
            'default' => 1
        ]
    ];
    
    return $sdk->admin()->servers()->createServer($serverData);
}

$server = createMinecraftServer($sdk, 1, 1);
echo "Server created with ID: " . $server['attributes']['id'] . "\n";
```

### Bulk Server Operations

```php
<?php
function bulkStartServers($sdk, $serverIds) {
    $results = [];
    
    foreach ($serverIds as $serverId) {
        try {
            $sdk->client()->servers()->startServer($serverId);
            $results[$serverId] = 'success';
        } catch (Exception $e) {
            $results[$serverId] = 'error: ' . $e->getMessage();
        }
    }
    
    return $results;
}

$serverIds = ['abc123def', 'def456ghi', 'ghi789jkl'];
$results = bulkStartServers($sdk, $serverIds);

foreach ($results as $serverId => $result) {
    echo "Server $serverId: $result\n";
}
```

## File Management Examples

### Upload Configuration File

```php
<?php
function uploadServerConfig($sdk, $serverId, $configPath, $remotePath) {
    if (!file_exists($configPath)) {
        throw new Exception("Config file not found: $configPath");
    }
    
    $content = file_get_contents($configPath);
    $sdk->client()->files()->writeFile($serverId, $remotePath, $content);
    
    echo "Config uploaded to $remotePath\n";
}

uploadServerConfig($sdk, 'abc123def', '/local/server.properties', '/server.properties');
```

### Backup Server Files

```php
<?php
function backupServerFiles($sdk, $serverId, $backupPath) {
    // Create backup directory
    $sdk->client()->files()->createDirectory($serverId, $backupPath);
    
    // Get list of files to backup
    $files = $sdk->client()->files()->listFiles($serverId, '/');
    $filesToBackup = [];
    
    foreach ($files['data'] as $file) {
        if ($file['attributes']['mimetype'] !== 'inode/directory') {
            $filesToBackup[] = $file['attributes']['name'];
        }
    }
    
    // Compress files
    $backupFile = $backupPath . '/backup_' . date('Y-m-d_H-i-s') . '.zip';
    $sdk->client()->files()->compressFiles($serverId, $filesToBackup, $backupFile);
    
    echo "Backup created: $backupFile\n";
}

backupServerFiles($sdk, 'abc123def', '/backups');
```

### Monitor Server Logs

```php
<?php
function monitorServerLogs($sdk, $serverId, $logFile = '/logs/latest.log') {
    $lastSize = 0;
    
    while (true) {
        try {
            $content = $sdk->client()->files()->getFileContents($serverId, $logFile);
            $currentSize = strlen($content);
            
            if ($currentSize > $lastSize) {
                $newContent = substr($content, $lastSize);
                echo $newContent;
                $lastSize = $currentSize;
            }
            
            sleep(1); // Check every second
        } catch (Exception $e) {
            echo "Error reading logs: " . $e->getMessage() . "\n";
            sleep(5); // Wait longer on error
        }
    }
}

// monitorServerLogs($sdk, 'abc123def'); // Uncomment to run
```

## Database Management Examples

### Create Database for Server

```php
<?php
function setupServerDatabase($sdk, $serverId, $databaseName) {
    // Create database
    $database = $sdk->client()->databases()->createDatabase($serverId, [
        'database' => $databaseName,
        'remote' => '%'
    ]);
    
    $dbId = $database['attributes']['id'];
    
    // Get connection details
    $databases = $sdk->client()->databases()->listDatabases($serverId);
    $dbDetails = null;
    
    foreach ($databases['data'] as $db) {
        if ($db['attributes']['id'] == $dbId) {
            $dbDetails = $db;
            break;
        }
    }
    
    if ($dbDetails) {
        echo "Database created successfully!\n";
        echo "Host: " . $dbDetails['attributes']['host']['address'] . "\n";
        echo "Port: " . $dbDetails['attributes']['host']['port'] . "\n";
        echo "Database: " . $dbDetails['attributes']['name'] . "\n";
        echo "Username: " . $dbDetails['attributes']['username'] . "\n";
        echo "Password: " . $dbDetails['attributes']['password'] . "\n";
    }
    
    return $dbDetails;
}

$dbDetails = setupServerDatabase($sdk, 'abc123def', 'minecraft_db');
```

## User Management Examples

### Create User with Server

```php
<?php
function createUserWithServer($sdk, $userData, $serverData) {
    // Create user
    $user = $sdk->admin()->users()->createUser($userData);
    $userId = $user['attributes']['id'];
    
    // Update server data with user ID
    $serverData['user'] = $userId;
    
    // Create server for user
    $server = $sdk->admin()->servers()->createServer($serverData);
    
    return [
        'user' => $user,
        'server' => $server
    ];
}

$userData = [
    'email' => 'newuser@example.com',
    'username' => 'newuser',
    'first_name' => 'New',
    'last_name' => 'User',
    'password' => 'securepassword123',
    'root_admin' => false
];

$serverData = [
    'name' => 'New User Server',
    'node' => 1,
    'nest' => 1,
    'egg' => 1,
    'docker_image' => 'quay.io/pterodactyl/core:java',
    'startup' => 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}',
    'environment' => [
        'SERVER_JARFILE' => 'server.jar',
        'SERVER_MEMORY' => '1024'
    ],
    'limits' => [
        'memory' => 1024,
        'swap' => 0,
        'disk' => 10240,
        'io' => 500,
        'cpu' => 100
    ],
    'feature_limits' => [
        'databases' => 1,
        'allocations' => 1,
        'backups' => 1
    ],
    'allocation' => [
        'default' => 1
    ]
];

$result = createUserWithServer($sdk, $userData, $serverData);
echo "User and server created successfully!\n";
```

### Bulk User Operations

```php
<?php
function bulkCreateUsers($sdk, $usersData) {
    $results = [];
    
    foreach ($usersData as $userData) {
        try {
            $user = $sdk->admin()->users()->createUser($userData);
            $results[] = [
                'email' => $userData['email'],
                'status' => 'success',
                'user_id' => $user['attributes']['id']
            ];
        } catch (Exception $e) {
            $results[] = [
                'email' => $userData['email'],
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $results;
}

$usersData = [
    [
        'email' => 'user1@example.com',
        'username' => 'user1',
        'first_name' => 'User',
        'last_name' => 'One',
        'password' => 'password123'
    ],
    [
        'email' => 'user2@example.com',
        'username' => 'user2',
        'first_name' => 'User',
        'last_name' => 'Two',
        'password' => 'password123'
    ]
];

$results = bulkCreateUsers($sdk, $usersData);
foreach ($results as $result) {
    echo "User {$result['email']}: {$result['status']}\n";
}
```

## Monitoring and Statistics

### Server Resource Monitoring

```php
<?php
function monitorServerResources($sdk, $serverId, $duration = 300) {
    $startTime = time();
    $data = [];
    
    while ((time() - $startTime) < $duration) {
        try {
            $resources = $sdk->client()->servers()->getServerResources($serverId);
            $data[] = [
                'timestamp' => time(),
                'cpu' => $resources['attributes']['resources']['cpu_absolute'],
                'memory' => $resources['attributes']['resources']['memory_bytes'],
                'disk' => $resources['attributes']['resources']['disk_bytes'],
                'network' => [
                    'rx' => $resources['attributes']['resources']['network']['rx_bytes'],
                    'tx' => $resources['attributes']['resources']['network']['tx_bytes']
                ]
            ];
            
            sleep(10); // Check every 10 seconds
        } catch (Exception $e) {
            echo "Error getting resources: " . $e->getMessage() . "\n";
            sleep(30); // Wait longer on error
        }
    }
    
    return $data;
}

$resourceData = monitorServerResources($sdk, 'abc123def', 60); // Monitor for 1 minute
echo "Collected " . count($resourceData) . " data points\n";
```

### Server Status Dashboard

```php
<?php
function generateServerDashboard($sdk) {
    $servers = $sdk->admin()->servers()->listServers();
    $dashboard = [];
    
    foreach ($servers['data'] as $server) {
        $serverId = $server['attributes']['id'];
        $serverName = $server['attributes']['name'];
        $status = $server['attributes']['status'];
        
        try {
            $resources = $sdk->client()->servers()->getServerResources($serverId);
            $dashboard[] = [
                'id' => $serverId,
                'name' => $serverName,
                'status' => $status,
                'cpu' => $resources['attributes']['resources']['cpu_absolute'],
                'memory' => $resources['attributes']['resources']['memory_bytes'],
                'disk' => $resources['attributes']['resources']['disk_bytes']
            ];
        } catch (Exception $e) {
            $dashboard[] = [
                'id' => $serverId,
                'name' => $serverName,
                'status' => $status,
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $dashboard;
}

$dashboard = generateServerDashboard($sdk);
foreach ($dashboard as $server) {
    echo "Server: {$server['name']} ({$server['status']})\n";
    if (isset($server['error'])) {
        echo "  Error: {$server['error']}\n";
    } else {
        echo "  CPU: {$server['cpu']}%\n";
        echo "  Memory: " . number_format($server['memory'] / 1024 / 1024, 2) . " MB\n";
        echo "  Disk: " . number_format($server['disk'] / 1024 / 1024, 2) . " MB\n";
    }
    echo "---\n";
}
```

## Advanced Examples

### Server Migration Script

```php
<?php
function migrateServer($sdk, $serverId, $newNodeId) {
    // Get server details
    $server = $sdk->admin()->servers()->getServer($serverId);
    
    // Stop server
    $sdk->client()->servers()->stopServer($serverId);
    
    // Wait for server to stop
    do {
        sleep(5);
        $server = $sdk->admin()->servers()->getServer($serverId);
        $status = $server['attributes']['status'];
    } while ($status !== 'offline');
    
    // Update server node
    $sdk->admin()->servers()->updateServer($serverId, [
        'node' => $newNodeId
    ]);
    
    // Start server on new node
    $sdk->client()->servers()->startServer($serverId);
    
    echo "Server migrated successfully!\n";
}

migrateServer($sdk, 1, 2);
```

### Automated Backup System

```php
<?php
function automatedBackupSystem($sdk) {
    $servers = $sdk->admin()->servers()->listServers();
    
    foreach ($servers['data'] as $server) {
        $serverId = $server['attributes']['identifier'];
        $serverName = $server['attributes']['name'];
        
        try {
            // Create backup
            $backup = $sdk->client()->servers()->createBackup($serverId, [
                'name' => 'Automated Backup - ' . date('Y-m-d H:i:s')
            ]);
            
            echo "Backup created for server: $serverName\n";
            
            // Clean up old backups (keep only last 5)
            $backups = $sdk->client()->servers()->listBackups($serverId);
            if (count($backups['data']) > 5) {
                $backupsToDelete = array_slice($backups['data'], 5);
                foreach ($backupsToDelete as $backup) {
                    $sdk->client()->servers()->deleteBackup($serverId, $backup['attributes']['uuid']);
                    echo "Deleted old backup: {$backup['attributes']['name']}\n";
                }
            }
            
        } catch (Exception $e) {
            echo "Failed to backup server $serverName: " . $e->getMessage() . "\n";
        }
    }
}

automatedBackupSystem($sdk);
```

### Resource Usage Alert System

```php
<?php
function checkResourceUsage($sdk, $thresholds = []) {
    $defaultThresholds = [
        'cpu' => 80,
        'memory' => 85,
        'disk' => 90
    ];
    
    $thresholds = array_merge($defaultThresholds, $thresholds);
    $alerts = [];
    
    $servers = $sdk->admin()->servers()->listServers();
    
    foreach ($servers['data'] as $server) {
        $serverId = $server['attributes']['identifier'];
        $serverName = $server['attributes']['name'];
        
        try {
            $resources = $sdk->client()->servers()->getServerResources($serverId);
            $cpu = $resources['attributes']['resources']['cpu_absolute'];
            $memory = ($resources['attributes']['resources']['memory_bytes'] / $resources['attributes']['resources']['memory_limit']) * 100;
            $disk = ($resources['attributes']['resources']['disk_bytes'] / $resources['attributes']['resources']['disk_limit']) * 100;
            
            $serverAlerts = [];
            
            if ($cpu > $thresholds['cpu']) {
                $serverAlerts[] = "CPU usage: {$cpu}% (threshold: {$thresholds['cpu']}%)";
            }
            
            if ($memory > $thresholds['memory']) {
                $serverAlerts[] = "Memory usage: " . number_format($memory, 2) . "% (threshold: {$thresholds['memory']}%)";
            }
            
            if ($disk > $thresholds['disk']) {
                $serverAlerts[] = "Disk usage: " . number_format($disk, 2) . "% (threshold: {$thresholds['disk']}%)";
            }
            
            if (!empty($serverAlerts)) {
                $alerts[$serverName] = $serverAlerts;
            }
            
        } catch (Exception $e) {
            $alerts[$serverName] = ["Error getting resources: " . $e->getMessage()];
        }
    }
    
    return $alerts;
}

$alerts = checkResourceUsage($sdk);
if (!empty($alerts)) {
    echo "Resource Usage Alerts:\n";
    foreach ($alerts as $serverName => $serverAlerts) {
        echo "Server: $serverName\n";
        foreach ($serverAlerts as $alert) {
            echo "  - $alert\n";
        }
        echo "\n";
    }
} else {
    echo "No resource usage alerts.\n";
}
```
