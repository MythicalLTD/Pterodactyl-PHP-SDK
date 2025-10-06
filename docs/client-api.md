# Client API

The Client API provides access to user-specific functions of the Pterodactyl Panel. This includes managing user's servers, files, databases, and account settings.

## Getting Started

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize client
$client = PterodactylSDK::clientOnly(
    'https://your-panel.com',
    'your-client-api-key'
);
```

## Server Management

### List User's Servers

```php
<?php
$servers = $client->servers()->listServers();

foreach ($servers['data'] as $server) {
    echo "Server: " . $server['attributes']['name'] . "\n";
    echo "Identifier: " . $server['attributes']['identifier'] . "\n";
    echo "Status: " . $server['attributes']['status'] . "\n";
    echo "---\n";
}
```

### Get Server Details

```php
<?php
$server = $client->servers()->getServer('abc123def');
echo "Server Name: " . $server['attributes']['name'] . "\n";
echo "Status: " . $server['attributes']['status'] . "\n";
echo "Memory: " . $server['attributes']['limits']['memory'] . " MB\n";
echo "Disk: " . $server['attributes']['limits']['disk'] . " MB\n";
```

### Send Power Commands

```php
<?php
// Available power commands: start, stop, restart, kill
$client->servers()->sendPowerCommand('abc123def', 'start');
echo "Server start command sent!";

// You can also use the specific methods
$client->servers()->startServer('abc123def');
$client->servers()->stopServer('abc123def');
$client->servers()->restartServer('abc123def');
$client->servers()->killServer('abc123def');
```

### Send Console Commands

```php
<?php
$client->servers()->sendConsoleCommand('abc123def', 'say Hello World!');
echo "Console command sent!";
```

### Get Server Resources

```php
<?php
$resources = $client->servers()->getServerResources('abc123def');
echo "CPU Usage: " . $resources['attributes']['current_state'] . "\n";
echo "Memory Usage: " . $resources['attributes']['resources']['memory_bytes'] . " bytes\n";
echo "Disk Usage: " . $resources['attributes']['resources']['disk_bytes'] . " bytes\n";
```

## File Management

### List Files in Directory

```php
<?php
$files = $client->files()->listFiles('abc123def', '/');

foreach ($files['data'] as $file) {
    echo "File: " . $file['attributes']['name'] . "\n";
    echo "Type: " . $file['attributes']['mimetype'] . "\n";
    echo "Size: " . $file['attributes']['size'] . " bytes\n";
    echo "---\n";
}
```

### Get File Contents

```php
<?php
$fileContent = $client->files()->getFileContents('abc123def', '/server.properties');
echo "File contents:\n" . $fileContent;
```

### Write File Contents

```php
<?php
$content = "server-name=My Server\nmax-players=20\n";
$client->files()->writeFile('abc123def', '/server.properties', $content);
echo "File written successfully!";
```

### Rename File or Directory

```php
<?php
$client->files()->renameFile('abc123def', '/old-name.txt', '/new-name.txt');
echo "File renamed successfully!";
```

### Copy File or Directory

```php
<?php
$client->files()->copyFile('abc123def', '/source.txt', '/destination.txt');
echo "File copied successfully!";
```

### Delete File or Directory

```php
<?php
$client->files()->deleteFile('abc123def', '/unwanted-file.txt');
echo "File deleted successfully!";
```

### Compress Files

```php
<?php
$files = ['/file1.txt', '/file2.txt', '/folder/'];
$client->files()->compressFiles('abc123def', $files, '/archive.zip');
echo "Files compressed successfully!";
```

### Decompress Archive

```php
<?php
$client->files()->decompressFile('abc123def', '/archive.zip', '/extracted/');
echo "Archive decompressed successfully!";
```

## Database Management

### List Databases

```php
<?php
$databases = $client->databases()->listDatabases('abc123def');

foreach ($databases['data'] as $database) {
    echo "Database: " . $database['attributes']['name'] . "\n";
    echo "Username: " . $database['attributes']['username'] . "\n";
    echo "Host: " . $database['attributes']['host']['address'] . "\n";
    echo "---\n";
}
```

### Create Database

```php
<?php
$database = $client->databases()->createDatabase('abc123def', [
    'database' => 'my_database',
    'remote' => '%'
]);
echo "Database created successfully!";
```

### Delete Database

```php
<?php
$client->databases()->deleteDatabase('abc123def', 1);
echo "Database deleted successfully!";
```

### Reset Database Password

```php
<?php
$client->databases()->resetPassword('abc123def', 1);
echo "Database password reset successfully!";
```

## Network Management

### List Allocations

```php
<?php
$allocations = $client->networks()->listAllocations('abc123def');

foreach ($allocations['data'] as $allocation) {
    echo "IP: " . $allocation['attributes']['ip'] . "\n";
    echo "Port: " . $allocation['attributes']['port'] . "\n";
    echo "Alias: " . $allocation['attributes']['alias'] . "\n";
    echo "---\n";
}
```

### Create Allocation

```php
<?php
$allocation = $client->networks()->createAllocation('abc123def', [
    'ip' => '192.168.1.100',
    'ports' => ['25565', '25566']
]);
echo "Allocation created successfully!";
```

### Delete Allocation

```php
<?php
$client->networks()->deleteAllocation('abc123def', 1);
echo "Allocation deleted successfully!";
```

### Set Primary Allocation

```php
<?php
$client->networks()->setPrimaryAllocation('abc123def', 1);
echo "Primary allocation set successfully!";
```

## Account Management

### Get Account Details

```php
<?php
$account = $client->getAccountDetails();
echo "Email: " . $account['attributes']['email'] . "\n";
echo "Username: " . $account['attributes']['username'] . "\n";
echo "First Name: " . $account['attributes']['first_name'] . "\n";
echo "Last Name: " . $account['attributes']['last_name'] . "\n";
```

### Update Email Address

```php
<?php
$client->updateEmail('newemail@example.com');
echo "Email updated successfully!";
```

### Update Password

```php
<?php
$client->updatePassword('currentpassword', 'newpassword');
echo "Password updated successfully!";
```

### Get API Keys

```php
<?php
$apiKeys = $client->getApiKeys();

foreach ($apiKeys['data'] as $key) {
    echo "Key: " . $key['attributes']['identifier'] . "\n";
    echo "Description: " . $key['attributes']['description'] . "\n";
    echo "Last Used: " . $key['attributes']['last_used_at'] . "\n";
    echo "---\n";
}
```

### Create API Key

```php
<?php
$apiKey = $client->createApiKey('My New API Key', ['192.168.1.100']);
echo "API Key created: " . $apiKey['attributes']['identifier'];
```

### Delete API Key

```php
<?php
$client->deleteApiKey('abc123def');
echo "API key deleted successfully!";
```

## SSH Key Management

### List SSH Keys

```php
<?php
$sshKeys = $client->sshKeys()->listKeys();

foreach ($sshKeys['data'] as $key) {
    echo "Key: " . $key['attributes']['name'] . "\n";
    echo "Fingerprint: " . $key['attributes']['fingerprint'] . "\n";
    echo "Created: " . $key['attributes']['created_at'] . "\n";
    echo "---\n";
}
```

### Create SSH Key

```php
<?php
$publicKey = "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQC... user@host";
$sshKey = $client->sshKeys()->createKey('My SSH Key', $publicKey);
echo "SSH key created successfully!";
```

### Delete SSH Key

```php
<?php
$client->sshKeys()->deleteKey(1);
echo "SSH key deleted successfully!";
```

## Activity Logs

### Get Server Activity

```php
<?php
$activity = $client->activity()->getServerActivity('abc123def');

foreach ($activity['data'] as $log) {
    echo "Event: " . $log['attributes']['event'] . "\n";
    echo "Description: " . $log['attributes']['description'] . "\n";
    echo "Timestamp: " . $log['attributes']['timestamp'] . "\n";
    echo "---\n";
}
```

### Get User Activity

```php
<?php
$activity = $client->activity()->getUserActivity();

foreach ($activity['data'] as $log) {
    echo "Event: " . $log['attributes']['event'] . "\n";
    echo "Description: " . $log['attributes']['description'] . "\n";
    echo "Timestamp: " . $log['attributes']['timestamp'] . "\n";
    echo "---\n";
}
```

## Schedules

### List Schedules

```php
<?php
$schedules = $client->schedules()->listSchedules('abc123def');

foreach ($schedules['data'] as $schedule) {
    echo "Schedule: " . $schedule['attributes']['name'] . "\n";
    echo "Cron: " . $schedule['attributes']['cron']['day_of_week'] . "\n";
    echo "Active: " . ($schedule['attributes']['is_active'] ? 'Yes' : 'No') . "\n";
    echo "---\n";
}
```

### Create Schedule

```php
<?php
$schedule = $client->schedules()->createSchedule('abc123def', [
    'name' => 'Daily Backup',
    'cron' => [
        'minute' => '0',
        'hour' => '2',
        'day_of_week' => '*',
        'day_of_month' => '*',
        'month' => '*'
    ],
    'is_active' => true
]);
echo "Schedule created successfully!";
```

### Execute Schedule

```php
<?php
$client->schedules()->executeSchedule('abc123def', 1);
echo "Schedule executed successfully!";
```

## Startup Management

### Get Startup Parameters

```php
<?php
$startup = $client->startup()->getStartup('abc123def');
echo "Startup Command: " . $startup['attributes']['startup'] . "\n";
echo "Raw Startup: " . $startup['attributes']['raw_startup'] . "\n";
```

### Update Startup Parameters

```php
<?php
$client->startup()->updateStartup('abc123def', [
    'startup' => 'java -Xms128M -Xmx1024M -jar server.jar',
    'environment' => [
        'SERVER_JARFILE' => 'server.jar',
        'SERVER_MEMORY' => '1024'
    ]
]);
echo "Startup parameters updated successfully!";
```

## Error Handling

All client API methods can throw the following exceptions:

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
    $server = $client->servers()->getServer('invalid-id');
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
