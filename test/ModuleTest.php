<?php

/**
 * This file contains the ModuleTest class for the Forge API
 * @license BSD-3-Clause
 */

declare(strict_types=1);

namespace Edu\Iu\Uits\Webtech\ForgeApi\Test;

use Edu\Iu\Uits\Webtech\ForgeApi\Exception\InvalidNameException;
use Edu\Iu\Uits\Webtech\ForgeApi\Module;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleTest
 * @package Edu\Iu\Uits\Webtech\ForgeApi\Test
 */
class ModuleTest extends TestCase
{
    public function testConstructWithInvalidName()
    {
        $this->expectException(InvalidNameException::class);
        $module = new Module('(*&)(*&^)(*&^&()*&G', new Client());
    }
    
    public function testFetch()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/module_fetch.json')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $module = new Module('puppetlabs-apache', $client);
        $result = $module->fetch();

        $this->assertEquals('modules/puppetlabs-apache', $historyContainer[0]['request']->getUri());
        $this->assertEquals('puppetlabs-apache', $result->slug);
    }

    public function testDelete()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([
            new Response(204),
            new Response(400),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $module = new Module('puppetlabs-apache', $client);
        $result = $module->delete('Broken code');
        $this->assertTrue($result);
        $event = $historyContainer[0];
        $this->assertEquals('DELETE', $event['request']->getMethod());
        $this->assertEquals('reason=Broken%20code', $event['request']->getUri()->getQuery());

        $this->assertFalse($module->delete('Broken code'));
    }
    
    public function testDeprecate()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([
            new Response(204),
            new Response(400)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $module = new Module('puppetlabs-apache', $client);
        $result = $module->deprecate('No longer maintained', 'puppet-nginx');
        $this->assertTrue($result);
        $event = $historyContainer[0];
        $this->assertEquals('PATCH', $event['request']->getMethod());
        $this->assertEquals('{"action":"deprecate","params":{"reason":"No longer maintained","replacement_slug":"puppet-nginx"}}', $event['request']->getBody()->getContents());

        $this->assertFalse($module->deprecate());
    }

    public function testDeprecateBadReplacement()
    {
        $this->expectException(InvalidNameException::class);
        $module = new Module('puppetlabs-apache', new Client());
        $module->deprecate('No longer maintained', '(#*!&$CHUHhu98ao8eu8g98g8');
    }
    
    public function testList()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/module_list.json')),
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/module_list.json')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $module = new Module(null, $client);
        $result = $module->list();

        $this->assertEquals('modules', $historyContainer[0]['request']->getUri());
        $this->assertInstanceOf('Edu\Iu\Uits\Webtech\ForgeApi\ForgeList', $result);
        $this->assertEquals(1, count($result->getResults()));

        $module->list(['limit' => 5]);
        $this->assertEquals('limit=5', $historyContainer[1]['request']->getUri()->getQuery());
    }
}
