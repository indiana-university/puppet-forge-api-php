<?php

/**
 * This file contains the module operations for the Puppet Forge REST API
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
 * Class Module
 * @package Edu\Iu\Uits\Webtech\ForgeApi
 */
class Module
{
    use NameValidationTrait;
    use FetchListTrait;
    use FetchObjectTrait;

    public const VALID_NAME = '^[a-zA-Z0-9]+[-\/][a-z][a-z0-9_]*$';
    public const PATH = 'modules/';

    /** @var string|null Then name of the module with which to work */
    private $name;

    /** @var Client a valid GuzzleHttp\Client object instance */
    private $client;

    /**
     * Module constructor.
     * @param string|null $name
     * @param Client $client
     * @throws InvalidNameException
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
     * @param array<string,mixed> $parameters Please see https://forgeapi.puppet.com/#operation/getModules
     * @param DateTime $ifModifiedSince
     * @return ForgeList|null
     * @throws EmptyResultSetException
     */
    public function list(
        array $parameters = [],
        DateTime $ifModifiedSince = null
    ): ?ForgeList {
        return $this->fetchList($parameters, $ifModifiedSince, 'modules');
    }

    /**
     * @param bool $with_html Render markdown files to HTML before returning results
     * @param array<int, string> $includeFields List of top level keys to include in response object
     * @param array<int, string> $excludeFields List of top level keys to exclude from response object
     * @param DateTime $ifModifiedSince Timezone MUST be GMT
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
     * @return bool If the delete was successful
     */
    public function delete(
        string $reason
    ): bool {
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
            // This is empty so that we can trap errors and return a boolean
        }
        return false;
    }

    /**
     * @param string|null $reason
     * @param string|null $replacement
     * @return bool
     * @throws InvalidNameException
     */
    public function deprecate(
        string $reason = null,
        string $replacement = null
    ): bool {
        
        if (
            !is_null($replacement) &&
            !preg_match('/' . self::VALID_NAME . '/', $replacement)
        ) {
            throw new InvalidNameException($replacement . ' is not a valid module name.');
        }
        
        $input = [
            'action' => 'deprecate'
        ];
        
        if (!is_null($reason)) {
            $input['params']['reason'] = $reason;
        }
        
        if (!is_null($replacement)) {
            $input['params']['replacement_slug'] = $replacement;
        }
        try {
            $response = $this->client->request(
                'PATCH',
                self::PATH . $this->name,
                [
                    'json' => $input
                ]
            );

            if ($response->getStatusCode() == 204) {
                return true;
            }
        } catch (ClientException $e) {
            // This is empty so that we can trap errors and return a boolean
        }
        return false;
    }
}
