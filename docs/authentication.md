# Authentication

The Pterodactyl PHP SDK supports two types of API authentication:

## API Key Types

### Admin API Key
- Used for administrative operations
- Provides access to all panel resources
- Can manage servers, users, nodes, locations, etc.
- Generated in the Admin Panel under "API Credentials"

### Client API Key
- Used for client operations
- Provides access to user-specific resources
- Can manage user's own servers, files, databases, etc.
- Generated in the User Panel under "Account Settings" → "API Credentials"

## Getting API Keys

### Admin API Key

1. Log into your Pterodactyl Panel as an administrator
2. Navigate to **Admin Panel** → **API Credentials**
3. Click **Create New**
4. Fill in the required information:
   - **Description**: A descriptive name for the key
   - **Allowed IPs**: Leave empty for all IPs, or specify allowed IP addresses
5. Click **Create**
6. Copy the generated API key (it will only be shown once)

### Client API Key

1. Log into your Pterodactyl Panel as a user
2. Navigate to **Account Settings** → **API Credentials**
3. Click **Create New**
4. Fill in the required information:
   - **Description**: A descriptive name for the key
   - **Allowed IPs**: Leave empty for all IPs, or specify allowed IPs
5. Click **Create**
6. Copy the generated API key (it will only be shown once)

## Using API Keys

### Full SDK (Both Admin and Client)

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$sdk = new PterodactylSDK(
    'https://your-panel.com',        // Panel URL
    'ptlc_admin_xxxxxxxxxxxxx',      // Admin API Key
    'ptlc_client_xxxxxxxxxxxxx'      // Client API Key
);
```

### Admin Only

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$admin = PterodactylSDK::adminOnly(
    'https://your-panel.com',        // Panel URL
    'ptlc_admin_xxxxxxxxxxxxx'       // Admin API Key
);
```

### Client Only

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$client = PterodactylSDK::clientOnly(
    'https://your-panel.com',        // Panel URL
    'ptlc_client_xxxxxxxxxxxxx'      // Client API Key
);
```

## Security Best Practices

1. **Store API keys securely**: Never commit API keys to version control
2. **Use environment variables**:
   ```php
   $adminKey = $_ENV['PTERODACTYL_ADMIN_KEY'];
   $clientKey = $_ENV['PTERODACTYL_CLIENT_KEY'];
   ```

3. **Restrict IP addresses**: When creating API keys, specify allowed IP addresses
4. **Use descriptive names**: Give your API keys meaningful descriptions
5. **Rotate keys regularly**: Periodically regenerate your API keys
6. **Monitor usage**: Check your API key usage in the panel

## Environment Variables Example

Create a `.env` file:

```env
PTERODACTYL_PANEL_URL=https://your-panel.com
PTERODACTYL_ADMIN_KEY=ptlc_admin_xxxxxxxxxxxxx
PTERODACTYL_CLIENT_KEY=ptlc_client_xxxxxxxxxxxxx
```

Use with environment variables:

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

$sdk = new PterodactylSDK(
    $_ENV['PTERODACTYL_PANEL_URL'],
    $_ENV['PTERODACTYL_ADMIN_KEY'],
    $_ENV['PTERODACTYL_CLIENT_KEY']
);
```

## Testing Authentication

You can test your authentication by making a simple API call:

```php
<?php
try {
    // Test admin authentication
    $servers = $sdk->admin()->servers()->listServers();
    echo "Admin authentication successful!";
    
    // Test client authentication
    $account = $sdk->client()->getAccountDetails();
    echo "Client authentication successful!";
} catch (Exception $e) {
    echo "Authentication failed: " . $e->getMessage();
}
```
