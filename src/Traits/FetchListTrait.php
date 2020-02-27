<?php

/**
 * This file contains the List Fetch Trait
 * @license BSD-3-Clause
 */

namespace Edu\Iu\Uits\Webtech\ForgeApi\Traits;

use DateTime;
use Edu\Iu\Uits\Webtech\ForgeApi\Exception\EmptyResultSetException;
use Edu\Iu\Uits\Webtech\ForgeApi\ForgeApi;
use Edu\Iu\Uits\Webtech\ForgeApi\ForgeList;

/**
 * Traits FetchListTrait
 * @package Edu\Iu\Uits\Webtech\ForgeApi\Traits
 */
trait FetchListTrait
{
    /**
     * @param array<string, mixed> $parameters
     * @param DateTime|null $ifModifiedSince
     * @param string $uri
     * @return ForgeList
     * @throws EmptyResultSetException
     */
    private function fetchList(
        array $parameters,
        ?DateTime $ifModifiedSince,
        string $uri
    ): ForgeList {
        $options = [];

        if (count($parameters)) {
            $options['query'] = $parameters;
        }

        if (!is_null($ifModifiedSince)) {
            $options['headers']['If-Modified-Since'] = $ifModifiedSince->format(ForgeApi::DATE_TIME_FORMAT);
        }
        $response = $this->client->request(
            'GET',
            $uri,
            $options
        );

        if ($response->getStatusCode() == 304) {
            throw new EmptyResultSetException('Query yielded no results', 304);
        }

        return new ForgeList($response->getBody()->getContents(), $this->client);
    }
}
