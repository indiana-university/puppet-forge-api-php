<?php

/**
 * This file is the main class for the Puppet Forge REST API library
 * @license BSD-3-Clause
 */

declare(strict_types=1);

namespace Edu\Iu\Uits\Webtech\ForgeApi;

use GuzzleHttp\Client;

/**
 * Class ForgeApi
 * @package Edu\Iu\Uits\Webtech\ForgeApi
 */
class ForgeApi
{
    public const FORGE_API_VERSION = 'v3';
    public const DATE_TIME_FORMAT = 'D, d M Y H:i:s e';
    public const THIS_LIBRARY_VERSION = '0.1';
    
    /** @var Client This is how we interact with the Forge API */
    private $client;

    /**
     * @param string $apiKey
     * @param string $apiBaseUrl
     */
    public function __construct(
        string $apiKey,
        string $apiBaseUrl = 'https://forgeapi.puppet.com/' . self::FORGE_API_VERSION . '/'
    ) {
        $this->client = new Client([
            'base_uri' => $apiBaseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'User-Agent' => 'IndianaUniversity-PhpForgeApi/' . self::THIS_LIBRARY_VERSION
            ],
        ]);
    }

    /**
     * @param string|null $name
     * @return User
     * @throws Exception\InvalidNameException
     */
    public function user(?string $name = null): User
    {
        return new User($name, $this->client);
    }

    /**
     * @param string|null $name
     * @return Module
     * @throws Exception\InvalidNameException
     */
    public function module(?string $name = null): Module
    {
        return new Module($name, $this->client);
    }

    /**
     * @param string|null $name
     * @return Release
     * @throws \Exception
     */
    public function release(?string $name = null): Release
    {
        return new Release($name, $this->client);
    }
}
