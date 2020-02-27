<?php

/**
 * This file contains the Release class for the Forge API
 * @license BSD-3-Clause
 */

declare(strict_types=1);

namespace Edu\Iu\Uits\Webtech\ForgeApi;

use DateTime;
use Edu\Iu\Uits\Webtech\ForgeApi\Exception\EmptyResultSetException;
use Edu\Iu\Uits\Webtech\ForgeApi\Exception\InvalidNameException;
use Edu\Iu\Uits\Webtech\ForgeApi\Traits\FetchListTrait;
use Edu\Iu\Uits\Webtech\ForgeApi\Traits\FetchObjectTrait;
use Edu\Iu\Uits\Webtech\ForgeApi\Traits\NameValidationTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use stdClass;

/**
 * Class Release
 * @package Edu\Iu\Uits\Webtech\ForgeApi
 */
class Release
{
    use FetchListTrait;
    use FetchObjectTrait;
    use NameValidationTrait;

    public const PATH = 'releases/';
    public const VALID_NAME = '^[a-zA-Z0-9]+[-\/][a-z][a-z0-9_]*[-\/][0-9]+\.[0-9]+\.[0-9]+(?:[\-+].+)?$';

    /** @var string|null  */
    private $name;

    /** @var Client  */
    private $client;

    /**
     * Release constructor.
     * @param string|null $name
     * @param Client $client
     * @throws \Exception
     */
    public function __construct(?string $name = null, Client $client)
    {
        $this->name = $name;
        if (!is_null($name)) {
            $this->throwExceptionIfNameInvalid();
        }
        $this->client = $client;
    }

    /**
     * @param array<string, mixed> $parameters
     * @param DateTime|null $ifModifiedSince
     * @return ForgeList
     * @throws EmptyResultSetException
     */
    public function list(
        array $parameters = [],
        DateTime $ifModifiedSince = null
    ): ForgeList {
        return $this->fetchList($parameters, $ifModifiedSince, 'releases');
    }

    /**
     * @param string $file
     * @return stdClass
     */
    public function create(string $file): stdClass
    {
        $upload = ['file' => $file];
        $response = $this->client->request(
            'POST',
            'releases',
            [
                'json' => $upload
            ]
        );
        return json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param bool $with_html
     * @param array<int, string> $includeFields
     * @param array<int, string> $excludeFields
     * @param DateTime|null $ifModifiedSince
     * @return stdClass
     * @throws InvalidNameException
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
            self::PATH
        );
    }

    /**
     * @param string $reason
     * @return bool
     */
    public function delete(string $reason): bool
    {
        try {
            $reply = $this->client->request(
                'DELETE',
                self::PATH . $this->name,
                [
                    'query' => [
                        'reason' => $reason,
                    ],
                ]
            );

            if ($reply->getStatusCode() == 204) {
                return true;
            }
        } catch (ClientException $e) {
        }
        return false;
    }
}
