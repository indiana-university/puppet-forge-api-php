<?php

/**
 * This file contains the User class for the Forge API
 * @license BSD-3-Clause
 */

declare(strict_types=1);

namespace Edu\Iu\Uits\Webtech\ForgeApi;

use DateTime;
use Edu\Iu\Uits\Webtech\ForgeApi\Exception\EmptyResultSetException;
use Edu\Iu\Uits\Webtech\ForgeApi\Traits\FetchObjectTrait;
use Edu\Iu\Uits\Webtech\ForgeApi\Traits\NameValidationTrait;
use GuzzleHttp\Client;
use stdClass;
use Edu\Iu\Uits\Webtech\ForgeApi\Traits\FetchListTrait;

/**
 * Class User
 * @package Edu\Iu\Uits\Webtech\ForgeApi
 */
class User
{
    use FetchObjectTrait;
    use FetchListTrait;
    use NameValidationTrait;

    public const VALID_NAME = '^[a-zA-Z0-9]+$';

    /**
     * @var string|null
     */
    private $name;
    
    /**
     * @var Client
     */
    private $client;

    /**
     * User constructor.
     * @param string|null $name
     * @param Client $client
     * @throws Exception\InvalidNameException
     */
    public function __construct(?string $name, Client $client)
    {
        $this->name = $name;
        if (!is_null($name)) {
            $this->throwExceptionIfNameInvalid();
        }
        $this->client = $client;
    }

    /**
     * @param bool $with_html
     * @param array<int, string> $includeFields
     * @param array<int, string> $excludeFields
     * @param DateTime|null $ifModifiedSince
     * @return stdClass
     * @throws Exception\InvalidNameException
     */
    public function fetch(
        bool $with_html = false,
        array $includeFields = [],
        array $excludeFields = [],
        DateTime $ifModifiedSince = null
    ): stdClass {
        return $this->fetchObject(
            $with_html,
            $includeFields,
            $excludeFields,
            $ifModifiedSince,
            'users/'
        );
    }

    /**
     * @param array<string, mixed> $parameters Please see https://forgeapi.puppet.com/#operation/getUsers
     * @param DateTime|null $ifModifiedSince
     * @return ForgeList
     * @throws EmptyResultSetException
     */
    public function list(
        array $parameters = [],
        DateTime $ifModifiedSince = null
    ): ForgeList {
        return $this->fetchList($parameters, $ifModifiedSince, 'users');
    }
}
