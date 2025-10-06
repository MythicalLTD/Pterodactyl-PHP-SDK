# Admin API

The Admin API provides access to administrative functions of the Pterodactyl Panel. This includes managing servers, users, nodes, locations, and nests.

## Getting Started

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize admin client
$admin = PterodactylSDK::adminOnly(
    'https://your-panel.com',
    'your-admin-api-key'
);
```

## Server Management

### List All Servers

```php
<?php
// List all servers with pagination
$servers = $admin->servers()->listServers($page = 1, $perPage = 50);

foreach ($servers['data'] as $server) {
    echo "Server ID: " . $server['attributes']['id'] . "\n";
    echo "Name: " . $server['attributes']['name'] . "\n";
    echo "Status: " . $server['attributes']['status'] . "\n";
    echo "---\n";
}
```

### Get Server Details

```php
<?php
// Get server with all related data
$server = $admin->servers()->getServer(1, [
    'allocations', 'user', 'subusers', 'pack', 
    'nest', 'egg', 'variables', 'location', 'node', 'databases'
]);

echo "Server Name: " . $server['attributes']['name'] . "\n";
echo "Owner: " . $server['attributes']['user'] . "\n";
echo "Node: " . $server['attributes']['node'] . "\n";
echo "Location: " . $server['attributes']['location'] . "\n";
```

### Create Server

```php
<?php
$serverData = [
    'name' => 'My New Server',
    'user' => 1,                    // User ID
    'node' => 1,                    // Node ID
    'nest' => 1,                    // Nest ID
    'egg' => 1,                     // Egg ID
    'docker_image' => 'quay.io/pterodactyl/core:java',
    'startup' => 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}',
    'environment' => [
        'SERVER_JARFILE' => 'server.jar',
        'BUNGEE_VERSION' => 'latest',
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
        'default' => 1              // Allocation ID
    ]
];

$server = $admin->servers()->createServer($serverData);
echo "Server created with ID: " . $server['attributes']['id'];
```

### Update Server

```php
<?php
$updateData = [
    'name' => 'Updated Server Name',
    'limits' => [
        'memory' => 2048,
        'swap' => 0,
        'disk' => 20480,
        'io' => 1000,
        'cpu' => 200
    ]
];

$server = $admin->servers()->updateServer(1, $updateData);
echo "Server updated successfully!";
```

### Delete Server

```php
<?php
$admin->servers()->deleteServer(1);
echo "Server deleted successfully!";
```

## User Management

### List All Users

```php
<?php
$users = $admin->users()->listUsers();

foreach ($users['data'] as $user) {
    echo "User ID: " . $user['attributes']['id'] . "\n";
    echo "Email: " . $user['attributes']['email'] . "\n";
    echo "Username: " . $user['attributes']['username'] . "\n";
    echo "---\n";
}
```

### Get User Details

```php
<?php
$user = $admin->users()->getUser(1);
echo "User: " . $user['attributes']['username'] . "\n";
echo "Email: " . $user['attributes']['email'] . "\n";
echo "Created: " . $user['attributes']['created_at'] . "\n";
```

### Create User

```php
<?php
$userData = [
    'email' => 'newuser@example.com',
    'username' => 'newuser',
    'first_name' => 'New',
    'last_name' => 'User',
    'password' => 'securepassword123',
    'root_admin' => false
];

$user = $admin->users()->createUser($userData);
echo "User created with ID: " . $user['attributes']['id'];
```

### Update User

```php
<?php
$updateData = [
    'email' => 'updated@example.com',
    'first_name' => 'Updated',
    'last_name' => 'Name'
];

$user = $admin->users()->updateUser(1, $updateData);
echo "User updated successfully!";
```

### Delete User

```php
<?php
$admin->users()->deleteUser(1);
echo "User deleted successfully!";
```

## Location Management

### List All Locations

```php
<?php
$locations = $admin->locations()->listLocations();

foreach ($locations['data'] as $location) {
    echo "Location ID: " . $location['attributes']['id'] . "\n";
    echo "Short Code: " . $location['attributes']['short'] . "\n";
    echo "Description: " . $location['attributes']['long'] . "\n";
    echo "---\n";
}
```

### Get Location Details

```php
<?php
$location = $admin->locations()->getLocation(1);
echo "Location: " . $location['attributes']['long'] . "\n";
echo "Short Code: " . $location['attributes']['short'] . "\n";
```

### Create Location

```php
<?php
$locationData = [
    'short' => 'NYC',
    'long' => 'New York City, USA'
];

$location = $admin->locations()->createLocation($locationData);
echo "Location created with ID: " . $location['attributes']['id'];
```

### Update Location

```php
<?php
$updateData = [
    'short' => 'NYC2',
    'long' => 'New York City, USA - Datacenter 2'
];

$location = $admin->locations()->updateLocation(1, $updateData);
echo "Location updated successfully!";
```

### Delete Location

```php
<?php
$admin->locations()->deleteLocation(1);
echo "Location deleted successfully!";
```

## Node Management

### List All Nodes

```php
<?php
$nodes = $admin->nodes()->listNodes();

foreach ($nodes['data'] as $node) {
    echo "Node ID: " . $node['attributes']['id'] . "\n";
    echo "Name: " . $node['attributes']['name'] . "\n";
    echo "Location: " . $node['attributes']['location_id'] . "\n";
    echo "---\n";
}
```

### Get Node Details

```php
<?php
$node = $admin->nodes()->getNode(1);
echo "Node: " . $node['attributes']['name'] . "\n";
echo "FQDN: " . $node['attributes']['fqdn'] . "\n";
echo "Memory: " . $node['attributes']['memory'] . " MB\n";
echo "Disk: " . $node['attributes']['disk'] . " MB\n";
```

### Create Node

```php
<?php
$nodeData = [
    'name' => 'Node 2',
    'description' => 'Second node in NYC',
    'location_id' => 1,
    'fqdn' => 'node2.example.com',
    'scheme' => 'https',
    'behind_proxy' => false,
    'maintenance_mode' => false,
    'memory' => 16384,
    'memory_overallocate' => 0,
    'disk' => 1048576,
    'disk_overallocate' => 0,
    'upload_size' => 100,
    'daemon_sftp' => 2022,
    'daemon_listen' => 8080
];

$node = $admin->nodes()->createNode($nodeData);
echo "Node created with ID: " . $node['attributes']['id'];
```

### Update Node

```php
<?php
$updateData = [
    'name' => 'Updated Node Name',
    'description' => 'Updated description',
    'memory' => 32768
];

$node = $admin->nodes()->updateNode(1, $updateData);
echo "Node updated successfully!";
```

### Delete Node

```php
<?php
$admin->nodes()->deleteNode(1);
echo "Node deleted successfully!";
```

## Nest Management

### List All Nests

```php
<?php
$nests = $admin->nests()->listNests();

foreach ($nests['data'] as $nest) {
    echo "Nest ID: " . $nest['attributes']['id'] . "\n";
    echo "Name: " . $nest['attributes']['name'] . "\n";
    echo "Description: " . $nest['attributes']['description'] . "\n";
    echo "---\n";
}
```

### Get Nest Details

```php
<?php
$nest = $admin->nests()->getNest(1);
echo "Nest: " . $nest['attributes']['name'] . "\n";
echo "Description: " . $nest['attributes']['description'] . "\n";
```

### Create Nest

```php
<?php
$nestData = [
    'name' => 'Custom Nest',
    'description' => 'A custom nest for our applications'
];

$nest = $admin->nests()->createNest($nestData);
echo "Nest created with ID: " . $nest['attributes']['id'];
```

### Update Nest

```php
<?php
$updateData = [
    'name' => 'Updated Nest Name',
    'description' => 'Updated description'
];

$nest = $admin->nests()->updateNest(1, $updateData);
echo "Nest updated successfully!";
```

### Delete Nest

```php
<?php
$admin->nests()->deleteNest(1);
echo "Nest deleted successfully!";
```

## Error Handling

All admin API methods can throw the following exceptions:

- `AuthenticationException` - Invalid API key
- `PermissionException` - Insufficient permissions
- `ResourceNotFoundException` - Resource not found
- `ValidationException` - Invalid input data
- `RateLimitException` - Rate limit exceeded

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PermissionException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ResourceNotFoundException;

try {
    $server = $admin->servers()->getServer(999);
} catch (AuthenticationException $e) {
    echo "Authentication failed: " . $e->getMessage();
} catch (PermissionException $e) {
    echo "Permission denied: " . $e->getMessage();
} catch (ResourceNotFoundException $e) {
    echo "Server not found: " . $e->getMessage();
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage();
}
```
