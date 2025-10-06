<?php

/*
 * This file is part of FeatherPanel.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * Breaking any of the following rules will result in a permanent ban from the MythicalSystems community and all of its services.
 */

namespace MythicalSystems\SDK\Pterodactyl\Wings;

use MythicalSystems\SDK\Pterodactyl\Wings\Services\JwtService;
use MythicalSystems\SDK\Pterodactyl\Wings\Services\DockerService;
use MythicalSystems\SDK\Pterodactyl\Wings\Services\ServerService;
use MythicalSystems\SDK\Pterodactyl\Wings\Services\SystemService;
use MythicalSystems\SDK\Pterodactyl\Wings\Services\TransferService;

/**
 * Main Wings API Client.
 *
 * This is the main entry point for the Wings API client.
 * It provides access to different service classes for different API areas.
 */
class Wings
{
    private WingsConnection $connection;
    private SystemService $system;
    private ServerService $server;
    private DockerService $docker;
    private TransferService $transfer;
    private JwtService $jwt;

    /**
     * Create a new Wings client instance.
     *
     * @param string $host The Wings server hostname/IP
     * @param int $port The Wings server port (default: 8080)
     * @param string $protocol The protocol to use (http/https)
     * @param string $authToken The authentication token for Wings
     * @param int $timeout Request timeout in seconds (default: 30)
     */
    public function __construct(
        string $host,
        int $port = 8080,
        string $protocol = 'http',
        string $authToken = '',
        int $timeout = 30,
    ) {
        $this->connection = new WingsConnection($host, $port, $protocol, $authToken, $timeout);

        // Initialize service classes
        $this->system = new SystemService($this->connection);
        $this->server = new ServerService($this->connection);
        $this->docker = new DockerService($this->connection);
        $this->transfer = new TransferService($this->connection);

        // Initialize JWT service with node secret
        $this->jwt = new JwtService($authToken, '', $this->connection->getBaseUrl());
    }

    /**
     * Get the system service.
     */
    public function getSystem(): SystemService
    {
        return $this->system;
    }

    /**
     * Get the server service.
     */
    public function getServer(): ServerService
    {
        return $this->server;
    }

    /**
     * Get the Docker service.
     */
    public function getDocker(): DockerService
    {
        return $this->docker;
    }

    /**
     * Get the transfer service.
     */
    public function getTransfer(): TransferService
    {
        return $this->transfer;
    }

    /**
     * Get the JWT service.
     */
    public function getJwt(): JwtService
    {
        return $this->jwt;
    }

    /**
     * Get the underlying connection.
     */
    public function getConnection(): WingsConnection
    {
        return $this->connection;
    }

    /**
     * Test the connection to Wings.
     */
    public function testConnection(): bool
    {
        return $this->connection->testConnection();
    }

    /**
     * Set the authentication token.
     */
    public function setAuthToken(string $token): void
    {
        $this->connection->setAuthToken($token);
    }

    /**
     * Get the authentication token.
     */
    public function getAuthToken(): string
    {
        return $this->connection->getAuthToken();
    }

    /**
     * Get the base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->connection->getBaseUrl();
    }

    /**
     * Get the token generator.
     */
    public function getTokenGenerator(): Utils\TokenGenerator
    {
        return $this->connection->getTokenGenerator();
    }
}
