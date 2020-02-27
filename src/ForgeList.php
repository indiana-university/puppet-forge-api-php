<?php

/**
 * This file contains the ForgeList class for the puppet forge API
 * @license BSD-3-Clause
 */

declare(strict_types=1);

namespace Edu\Iu\Uits\Webtech\ForgeApi;

use GuzzleHttp\Client;
use stdClass;

/**
 * Class ForgeList
 * @package Edu\Iu\Uits\Webtech\ForgeApi
 */
class ForgeList
{
    /** @var int Results per page limit */
    private $limit;

    /** @var int Results offset from beginning */
    private $offset;

    /** @var string Link to first results page */
    private $first;
    
    /** @var string|null Link to previous results page */
    private $previous = null;

    /** @var string Link to current results page */
    private $current;
    
    /** @var string|null Link to next results page */
    private $next = null;
    
    /** @var int Total number of results across all pages */
    private $total;

    /** @var array<stdClass> The array of results
     */
    private $results = [];

    /** @var Client An instance of GuzzleHttp\Client */
    private $client;
    
    public function __construct(string $contents, Client $client)
    {
        $data = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);
        $this->limit = $data->pagination->limit;
        $this->offset = $data->pagination->offset;
        $this->first = $data->pagination->first;
        $this->previous = $data->pagination->previous;
        $this->current = $data->pagination->current;
        $this->next = $data->pagination->next;
        $this->total = $data->pagination->total;
        $this->results = $data->results;
        $this->client = $client;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return array<stdClass>
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return ForgeList
     */
    public function first(): ForgeList
    {
        $response = $this->client->request('GET', $this->first);
        return new ForgeList(
            $response->getBody()->getContents(),
            $this->client
        );
    }

    /**
     * @return ForgeList|null
     */
    public function previous(): ?ForgeList
    {
        if (!is_null($this->previous)) {
            $response = $this->client->request('GET', $this->previous);
            return new ForgeList(
                $response->getBody()->getContents(),
                $this->client
            );
        }
        return null;
    }

    /**
     * @return ForgeList|null
     */
    public function next(): ?ForgeList
    {
        if (!is_null($this->next)) {
            $response = $this->client->request('GET', $this->next);
            return new ForgeList(
                $response->getBody()->getContents(),
                $this->client
            );
        }
        return null;
    }
}
