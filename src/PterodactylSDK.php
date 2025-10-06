<?php

/*
 * This file is part of MythicalSystems Pterodactyl PHP SDK.
 *
 * MIT License
 *
 * Copyright (c) 2020-2025 MythicalSystems
 * Copyright (c) 2020-2025 Cassian Gherman (NaysKutzu)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace MythicalSystems\SDK\Pterodactyl;

use MythicalSystems\SDK\Pterodactyl\Admin\PterodactylAdmin;
use MythicalSystems\SDK\Pterodactyl\Client\PterodactylClient;
use MythicalSystems\SDK\Pterodactyl\Wings\Wings;

/**
 * Main SDK class for Pterodactyl Panel API
 * 
 * This class provides easy access to Admin, Client, and Wings APIs
 */
class PterodactylSDK
{
    private PterodactylAdmin $admin;
    private PterodactylClient $client;
    private Wings $wings;

    /**
     * Create a new Pterodactyl SDK instance
     *
     * @param string $baseUrl The base URL of your Pterodactyl panel
     * @param string $adminApiKey Admin API key for admin operations
     * @param string $clientApiKey Client API key for client operations
     * @param string|null $wingsHost Wings server hostname/IP (optional)
     * @param int $wingsPort Wings server port (default: 8080)
     * @param string $wingsProtocol Wings protocol (http/https, default: http)
     * @param string $wingsToken Wings authentication token (format: node-token-id.node-token-secret) (optional)
     */
    public function __construct(
        string $baseUrl, 
        string $adminApiKey, 
        string $clientApiKey,
        ?string $wingsHost = null,
        int $wingsPort = 8080,
        string $wingsProtocol = 'http',
        string $wingsToken = ''
    ) {
        $this->admin = new PterodactylAdmin($baseUrl, $adminApiKey);
        $this->client = new PterodactylClient($baseUrl, $clientApiKey);
        
        // Initialize Wings if host is provided
        if ($wingsHost) {
            $this->wings = new Wings($wingsHost, $wingsPort, $wingsProtocol, $wingsToken);
        }
    }

    /**
     * Get the Admin API client
     *
     * @return PterodactylAdmin
     */
    public function admin(): PterodactylAdmin
    {
        return $this->admin;
    }

    /**
     * Get the Client API client
     *
     * @return PterodactylClient
     */
    public function client(): PterodactylClient
    {
        return $this->client;
    }

    /**
     * Get the Wings API client
     *
     * @return Wings
     * @throws \Exception If Wings is not initialized
     */
    public function wings(): Wings
    {
        if (!isset($this->wings)) {
            throw new \Exception('Wings is not initialized. Provide wingsHost in constructor.');
        }
        return $this->wings;
    }

    /**
     * Create an Admin-only SDK instance
     *
     * @param  string $baseUrl     The base URL of your Pterodactyl panel
     * @param  string $adminApiKey Admin API key
     * @return PterodactylAdmin
     */
    public static function adminOnly(string $baseUrl, string $adminApiKey): PterodactylAdmin
    {
        return new PterodactylAdmin($baseUrl, $adminApiKey);
    }

    /**
     * Create a Client-only SDK instance
     *
     * @param  string $baseUrl      The base URL of your Pterodactyl panel
     * @param  string $clientApiKey Client API key
     * @return PterodactylClient
     */
    public static function clientOnly(string $baseUrl, string $clientApiKey): PterodactylClient
    {
        return new PterodactylClient($baseUrl, $clientApiKey);
    }

    /**
     * Create a Wings-only SDK instance
     *
     * @param string $wingsHost Wings server hostname/IP
     * @param int $wingsPort Wings server port (default: 8080)
     * @param string $wingsProtocol Wings protocol (http/https, default: http)
     * @param string $wingsToken Wings authentication token (format: node-token-id.node-token-secret)
     * @return Wings
     */
    public static function wingsOnly(
        string $wingsHost, 
        int $wingsPort = 8080, 
        string $wingsProtocol = 'http', 
        string $wingsToken = ''
    ): Wings {
        return new Wings($wingsHost, $wingsPort, $wingsProtocol, $wingsToken);
    }
}
