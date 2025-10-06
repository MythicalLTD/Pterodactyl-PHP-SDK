# Panel API Documentation

Welcome to the comprehensive Panel API documentation. The Panel API provides access to both Admin and Client functionality of your Pterodactyl installation.

## Table of Contents

- [Getting Started](getting-started.md)
- [Authentication](authentication.md)
- [Admin API](admin-api.md)
  - [Server Management](admin-api.md#server-management)
  - [User Management](admin-api.md#user-management)
  - [Location Management](admin-api.md#location-management)
  - [Node Management](admin-api.md#node-management)
  - [Nest Management](admin-api.md#nest-management)
- [Client API](client-api.md)
  - [Server Management](client-api.md#server-management)
  - [File Management](client-api.md#file-management)
  - [Database Management](client-api.md#database-management)
  - [Network Management](client-api.md#network-management)
  - [Account Management](client-api.md#account-management)
  - [SSH Key Management](client-api.md#ssh-key-management)
  - [Activity Logs](client-api.md#activity-logs)
  - [Schedules](client-api.md#schedules)
  - [Startup Management](client-api.md#startup-management)
- [Authentication](authentication.md) - API key setup and security
- [Error Handling](error-handling.md) - Exception management
- [Examples](examples.md) - Real-world scenarios

## What is the Panel API?

The Panel API consists of two main components:

### Admin API
- **Panel Administration** - Manage the entire Pterodactyl installation
- **Server Management** - Create, update, delete servers across all nodes
- **User Management** - Manage user accounts, permissions, and roles
- **Infrastructure** - Manage nodes, locations, nests, and eggs
- **System Configuration** - Configure panel settings and options

### Client API
- **User Operations** - Access user-specific resources and settings
- **Server Control** - Manage user's own servers and resources
- **File Management** - Upload, download, and manage server files
- **Database Access** - Create and manage databases for servers
- **Account Settings** - Manage user profile and API keys

## Quick Start

```php
<?php
use MythicalSystems\SDK\Pterodactyl\PterodactylSDK;

// Initialize Panel API client
$sdk = new PterodactylSDK(
    'https://your-panel.com',
    'ptlc_admin_xxxxxxxxxxxxx',    // Admin API Key
    'ptlc_client_xxxxxxxxxxxxx'    // Client API Key
);

// Use Admin API
$servers = $sdk->admin()->servers()->listServers();

// Use Client API
$account = $sdk->client()->getAccountDetails();
```

## API Overview

### Admin API Resources
- **Servers** - Complete server lifecycle management
- **Users** - User account and permission management
- **Locations** - Geographic server locations
- **Nodes** - Server nodes and resource allocation
- **Nests** - Application templates and configurations

### Client API Resources
- **Servers** - User server management and control
- **Files** - File operations and directory management
- **Databases** - Database creation and management
- **Networks** - Network allocation management
- **Account** - User account settings and API keys
- **SSH Keys** - Secure server access management
- **Activity** - Server and user activity logs
- **Schedules** - Automated task scheduling
- **Startup** - Server startup parameter management

## Getting Help

- [GitHub Issues](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/issues)
- [Documentation](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/tree/main/docs)
- [Contributing](https://github.com/mythicalltd/Pterodactyl-PHP-SDK/blob/main/CONTRIBUTING.md)
