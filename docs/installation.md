# Installation

## Requirements

- PHP 8.0 or higher
- Composer
- Pterodactyl Panel with API access

## Installation via Composer

Install the package using Composer:

```bash
composer require mythicalsystems/pterodactyl-php-sdk
```

## Manual Installation

If you prefer not to use Composer, you can download the source code and include the autoloader:

```php
<?php
require_once 'path/to/pterodactyl-php-sdk/vendor/autoload.php';
```

## Verify Installation

You can verify the installation by checking if the classes are available:

```php
<?php
require_once 'vendor/autoload.php';

// Check if the main SDK class exists
if (class_exists('MythicalSystems\SDK\Pterodactyl\PterodactylSDK')) {
    echo "Pterodactyl PHP SDK installed successfully!";
} else {
    echo "Installation failed.";
}
```

## Next Steps

After installation, proceed to:
- [Quick Start Guide](quick-start.md)
- [Panel API Documentation](panel/README.md)
- [Wings API Documentation](wings/README.md)
