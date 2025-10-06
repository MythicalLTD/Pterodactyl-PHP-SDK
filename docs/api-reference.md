# API Reference

Complete API reference for the Pterodactyl PHP SDK.

## Main SDK Class

### PterodactylSDK

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Full SDK with all APIs
$sdk = new PterodactylSDK(
    string $baseUrl,           // Panel URL
    string $adminApiKey,       // Admin API Key
    string $clientApiKey,      // Client API Key
    ?string $wingsHost = null, // Wings Host (optional)
    int $wingsPort = 8080,     // Wings Port
    string $wingsProtocol = 'http', // Wings Protocol
    string $wingsToken = ''    // Wings Token
);

// Factory methods
$admin = PterodactylSDK::adminOnly(string $baseUrl, string $adminApiKey);
$client = PterodactylSDK::clientOnly(string $baseUrl, string $clientApiKey);
$wings = PterodactylSDK::wingsOnly(string $wingsHost, int $wingsPort, string $wingsProtocol, string $wingsToken);

// Access APIs
$admin = $sdk->admin();   // PterodactylAdmin
$client = $sdk->client(); // PterodactylClient
$wings = $sdk->wings();   // Wings
```

## Panel API Classes

### PterodactylAdmin

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Admin\PterodactylAdmin;

$admin = new PterodactylAdmin(string $baseUrl, string $apiKey);

// Resource access
$servers = $admin->servers();     // ServersResource
$users = $admin->users();         // UsersResource
$locations = $admin->locations(); // LocationsResource
$nodes = $admin->nodes();         // NodesResource
$nests = $admin->nests();         // NestsResource
```

### PterodactylClient

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Client\PterodactylClient;

$client = new PterodactylClient(string $baseUrl, string $apiKey);

// Resource access
$servers = $client->servers();     // ServerResource
$files = $client->files();         // FileResource
$databases = $client->databases(); // DatabaseResource
$networks = $client->networks();   // NetworkResource
$account = $client->account();     // AccountResource
$sshKeys = $client->sshKeys();     // SSHKeyResource
$activity = $client->activity();   // ActivityResource
$schedules = $client->schedules(); // ScheduleResource
$startup = $client->startup();     // StartupResource
```

## Wings API Classes

### Wings

```php
<?php
use MythicalSystems\SDK\Pterodactyl\Wings\Wings;

$wings = new Wings(
    string $host,        // Wings hostname/IP
    int $port = 8080,    // Wings port
    string $protocol = 'http', // Protocol
    string $authToken = '',    // Wings token
    int $timeout = 30    // Request timeout
);

// Service access
$system = $wings->getSystem();     // SystemService
$server = $wings->getServer();     // ServerService
$docker = $wings->getDocker();     // DockerService
$transfer = $wings->getTransfer(); // TransferService
$jwt = $wings->getJwt();           // JwtService
```

## Admin API Resources

### ServersResource

```php
// List servers
$servers = $admin->servers()->listServers(int $page = 1, int $perPage = 50): array

// Get server
$server = $admin->servers()->getServer(int $serverId, array $includes = []): array

// Create server
$server = $admin->servers()->createServer(array $data): array

// Update server
$server = $admin->servers()->updateServer(int $serverId, array $data): array

// Delete server
$admin->servers()->deleteServer(int $serverId): void
```

### UsersResource

```php
// List users
$users = $admin->users()->listUsers(int $page = 1, int $perPage = 50): array

// Get user
$user = $admin->users()->getUser(int $userId): array

// Create user
$user = $admin->users()->createUser(array $data): array

// Update user
$user = $admin->users()->updateUser(int $userId, array $data): array

// Delete user
$admin->users()->deleteUser(int $userId): void
```

### LocationsResource

```php
// List locations
$locations = $admin->locations()->listLocations(int $page = 1, int $perPage = 50): array

// Get location
$location = $admin->locations()->getLocation(int $locationId): array

// Create location
$location = $admin->locations()->createLocation(array $data): array

// Update location
$location = $admin->locations()->updateLocation(int $locationId, array $data): array

// Delete location
$admin->locations()->deleteLocation(int $locationId): void
```

### NodesResource

```php
// List nodes
$nodes = $admin->nodes()->listNodes(int $page = 1, int $perPage = 50): array

// Get node
$node = $admin->nodes()->getNode(int $nodeId): array

// Create node
$node = $admin->nodes()->createNode(array $data): array

// Update node
$node = $admin->nodes()->updateNode(int $nodeId, array $data): array

// Delete node
$admin->nodes()->deleteNode(int $nodeId): void
```

### NestsResource

```php
// List nests
$nests = $admin->nests()->listNests(int $page = 1, int $perPage = 50): array

// Get nest
$nest = $admin->nests()->getNest(int $nestId): array

// Create nest
$nest = $admin->nests()->createNest(array $data): array

// Update nest
$nest = $admin->nests()->updateNest(int $nestId, array $data): array

// Delete nest
$admin->nests()->deleteNest(int $nestId): void
```

## Client API Resources

### ServerResource

```php
// List user's servers
$servers = $client->servers()->listServers(): array

// Get server
$server = $client->servers()->getServer(string $serverId): array

// Power commands
$client->servers()->startServer(string $serverId): void
$client->servers()->stopServer(string $serverId): void
$client->servers()->restartServer(string $serverId): void
$client->servers()->killServer(string $serverId): void

// Console commands
$client->servers()->sendConsoleCommand(string $serverId, string $command): void

// Resources
$resources = $client->servers()->getServerResources(string $serverId): array
```

### FileResource

```php
// List files
$files = $client->files()->listFiles(string $serverId, string $path): array

// File operations
$content = $client->files()->getFileContents(string $serverId, string $path): string
$client->files()->writeFile(string $serverId, string $path, string $content): void
$client->files()->renameFile(string $serverId, string $oldPath, string $newPath): void
$client->files()->copyFile(string $serverId, string $source, string $destination): void
$client->files()->deleteFile(string $serverId, string $path): void

// Directory operations
$client->files()->createDirectory(string $serverId, string $path): void

// Compression
$client->files()->compressFiles(string $serverId, array $files, string $destination): void
$client->files()->decompressFile(string $serverId, string $archive, string $destination): void
```

### DatabaseResource

```php
// List databases
$databases = $client->databases()->listDatabases(string $serverId): array

// Database operations
$database = $client->databases()->createDatabase(string $serverId, array $data): array
$client->databases()->deleteDatabase(string $serverId, int $databaseId): void
$client->databases()->resetPassword(string $serverId, int $databaseId): array
```

### NetworkResource

```php
// List allocations
$allocations = $client->networks()->listAllocations(string $serverId): array

// Allocation operations
$allocation = $client->networks()->createAllocation(string $serverId, array $data): array
$client->networks()->deleteAllocation(string $serverId, int $allocationId): void
$client->networks()->setPrimaryAllocation(string $serverId, int $allocationId): void
```

### AccountResource

```php
// Account operations
$account = $client->getAccountDetails(): array
$client->updateEmail(string $email): void
$client->updatePassword(string $currentPassword, string $newPassword): void

// API keys
$apiKeys = $client->getApiKeys(): array
$apiKey = $client->createApiKey(string $description, array $allowedIps = []): array
$client->deleteApiKey(string $identifier): void
```

## Wings API Services

### SystemService

```php
// System information
$info = $wings->getSystem()->getSystemInfo(): array
$stats = $wings->getSystem()->getSystemStats(): array
$resources = $wings->getSystem()->getSystemResources(): array
```

### ServerService

```php
// Server management
$servers = $wings->getServer()->listServers(): array
$server = $wings->getServer()->getServer(string $serverUuid): array
$server = $wings->getServer()->createServer(array $data): array
$wings->getServer()->updateServer(string $serverUuid, array $data): void
$wings->getServer()->deleteServer(string $serverUuid): void

// Power commands
$wings->getServer()->startServer(string $serverUuid): void
$wings->getServer()->stopServer(string $serverUuid): void
$wings->getServer()->restartServer(string $serverUuid): void
$wings->getServer()->killServer(string $serverUuid): void

// Console and logs
$wings->getServer()->sendCommand(string $serverUuid, string $command): void
$logs = $wings->getServer()->getServerLogs(string $serverUuid, int $lines = 100): array
$resources = $wings->getServer()->getServerResources(string $serverUuid): array
```

### DockerService

```php
// Container management
$containers = $wings->getDocker()->listContainers(): array
$container = $wings->getDocker()->getContainer(string $containerId): array
$wings->getDocker()->startContainer(string $containerId): void
$wings->getDocker()->stopContainer(string $containerId): void
$wings->getDocker()->restartContainer(string $containerId): void
$wings->getDocker()->killContainer(string $containerId): void
$wings->getDocker()->removeContainer(string $containerId): void

// Image management
$images = $wings->getDocker()->getImages(): array
$image = $wings->getDocker()->getImage(string $imageId): array
$wings->getDocker()->pullImage(string $imageName): void
$wings->getDocker()->removeImage(string $imageId): void
$result = $wings->getDocker()->pruneImages(): array
```

### TransferService

```php
// Transfer operations
$transfer = $wings->getTransfer()->startTransfer(array $data): array
$status = $wings->getTransfer()->getTransferStatus(string $transferId): array
$wings->getTransfer()->cancelTransfer(string $transferId): void
$transfers = $wings->getTransfer()->listTransfers(): array
```

### JwtService

```php
// Token generation
$token = $wings->getJwt()->generateServerToken(string $serverUuid, int $expiry = 3600): string
$token = $wings->getJwt()->generateFileToken(string $serverUuid, string $filePath, int $expiry = 3600): string
$token = $wings->getJwt()->generateConsoleToken(string $serverUuid, int $expiry = 3600): string
$token = $wings->getJwt()->generateDownloadToken(string $serverUuid, string $filePath, int $expiry = 3600): string

// Token validation
$claims = $wings->getJwt()->validateToken(string $token): array
$claims = $wings->getJwt()->getTokenClaims(string $token): array
```

## Exception Classes

### Panel API Exceptions

```php
use MythicalSystems\SDK\Pterodactyl\Exceptions\AuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PermissionException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ResourceNotFoundException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ValidationException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\RateLimitException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\ServerException;
use MythicalSystems\SDK\Pterodactyl\Exceptions\PterodactylException;
```

### Wings API Exceptions

```php
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsAuthenticationException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsConnectionException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsRequestException;
use MythicalSystems\SDK\Pterodactyl\Wings\Exceptions\WingsException;
```

## Response Formats

### Standard API Response

```php
[
    'data' => [
        // Resource data
    ],
    'meta' => [
        'pagination' => [
            'total' => 100,
            'count' => 50,
            'per_page' => 50,
            'current_page' => 1,
            'total_pages' => 2
        ]
    ]
]
```

### Error Response

```php
[
    'errors' => [
        [
            'code' => 'ValidationException',
            'status' => '422',
            'detail' => 'The given data was invalid.',
            'meta' => [
                'source_field' => 'name'
            ]
        ]
    ]
]
```

## Common Parameters

### Pagination

```php
$page = 1;        // Page number (default: 1)
$perPage = 50;    // Items per page (default: 50, max: 100)
```

### Includes

```php
$includes = [
    'allocations',  // Include allocation data
    'user',         // Include user data
    'subusers',     // Include subuser data
    'pack',         // Include pack data
    'nest',         // Include nest data
    'egg',          // Include egg data
    'variables',    // Include variable data
    'location',     // Include location data
    'node',         // Include node data
    'databases'     // Include database data
];
```

### Server Limits

```php
$limits = [
    'memory' => 1024,    // Memory limit in MB
    'swap' => 0,         // Swap limit in MB
    'disk' => 10240,     // Disk limit in MB
    'io' => 500,         // IO limit (1-1000)
    'cpu' => 100         // CPU limit (1-100)
];
```

### Feature Limits

```php
$featureLimits = [
    'databases' => 1,    // Number of databases
    'allocations' => 1,  // Number of allocations
    'backups' => 1       // Number of backups
];
```

## Best Practices

1. **Always handle exceptions** - Use try-catch blocks for all API calls
2. **Validate input data** - Check parameters before making API calls
3. **Use pagination** - For large datasets, use pagination parameters
4. **Cache responses** - Cache frequently accessed data
5. **Monitor rate limits** - Implement rate limiting in your application
6. **Use HTTPS** - Always use secure connections in production
7. **Store credentials securely** - Use environment variables for API keys
8. **Log API calls** - Log important API operations for debugging
