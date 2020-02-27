<?php

/**
 * This file contains the fetch object trait
 * @license BSD-3-Clause
 */

namespace Edu\Iu\Uits\Webtech\ForgeApi\Traits;

use DateTime;
use Edu\Iu\Uits\Webtech\ForgeApi\Exception\InvalidNameException;
use Edu\Iu\Uits\Webtech\ForgeApi\ForgeApi;
use stdClass;

/**
 * Trait FetchObjectTrait
 * @package Edu\Iu\Uits\Webtech\ForgeApi\Traits
 */
trait FetchObjectTrait
{
    /**
     * @param bool $with_html
     * @param array<int, string> $includeFields
     * @param array<int, string> $excludeFields
     * @param DateTime|null $ifModifiedSince
     * @param string $path
     * @return stdClass
     * @throws InvalidNameException
     */
    public function fetchObject(
        bool $with_html = false,
        array $includeFields = [],
        array $excludeFields = [],
        DateTime $ifModifiedSince = null,
        string $path = ''
    ): stdClass {
        $this->throwExceptionIfNameInvalid();

        $query = [];
        $headers = [];

        if ($with_html) {
            $query['with_html'] = $with_html;
        }

        if (count($includeFields)) {
            $query['include_fields'] = implode(' ', $includeFields);
        }

        if (count($excludeFields)) {
            $query['exclude_fields'] = implode(' ', $excludeFields);
        }

        if (!is_null($ifModifiedSince)) {
            $headers['If-Modified-Since'] = $ifModifiedSince->format(ForgeApi::DATE_TIME_FORMAT);
        }

        $result = $this->client->request(
            'GET',
            $path . $this->name,
            [
                'query' => $query,
                'headers' => $headers
            ]
        );
        return json_decode($result->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
    }
}
