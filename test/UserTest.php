<?php

/**
 * This file contains the UserTest class for the Forge API
 * @license BSD-3-Clause
 */

declare(strict_types=1);

namespace Edu\Iu\Uits\Webtech\ForgeApi\Test;

use Edu\Iu\Uits\Webtech\ForgeApi\Exception\InvalidNameException;
use Edu\Iu\Uits\Webtech\ForgeApi\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package Edu\Iu\Uits\Webtech\ForgeApi\Test
 */
class UserTest extends TestCase
{
    public function testConstructWithInvalidName()
    {
        $this->expectException(InvalidNameException::class);
        $module = new User('(*&)(*&^)(*&^&()*&G', new Client());
    }

    public function testFetch()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/user_fetch.json')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $user = new User('puppetlabs', $client);
        $result = $user->fetch();
        
        $this->assertEquals('users/puppetlabs', $historyContainer[0]['request']->getUri());
        $this->assertEquals('puppetlabs', $result->slug);
    }

    public function testList()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/user_list.json')),
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/user_list.json')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $user = new User(null, $client);
        $result = $user->list();

        $this->assertEquals('users', $historyContainer[0]['request']->getUri());
        $this->assertInstanceOf('Edu\Iu\Uits\Webtech\ForgeApi\ForgeList', $result);
        $this->assertEquals(1, count($result->getResults()));

        $user->list(['limit' => 5]);
        $this->assertEquals('limit=5', $historyContainer[1]['request']->getUri()->getQuery());
    }
}
