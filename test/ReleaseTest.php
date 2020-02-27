<?php

namespace Edu\Iu\Uits\Webtech\ForgeApi\Test;

use Edu\Iu\Uits\Webtech\ForgeApi\Release;
use Edu\Iu\Uits\Webtech\ForgeApi\Exception\InvalidNameException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ReleaseTest extends TestCase
{

    public function testList()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/release_list.json')),
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/release_list.json')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $module = new Release(null, $client);
        $result = $module->list();

        $this->assertEquals('releases', $historyContainer[0]['request']->getUri());
        $this->assertInstanceOf('Edu\Iu\Uits\Webtech\ForgeApi\ForgeList', $result);
        $this->assertEquals(1, count($result->getResults()));

        $module->list(['limit' => 5]);
        $this->assertEquals('limit=5', $historyContainer[1]['request']->getUri()->getQuery());
    }

    public function testCreate()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);

        $mock = new MockHandler([
            new Response(201, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/release_create.json')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);
        
        $release = new Release(null, $client);
        $result = $release->create('H4sIABYkiFwAA+09CT...');

        $this->assertEquals('puppetlabs-apache-4.0.0', $result->slug);
        $this->assertEquals('{"file":"H4sIABYkiFwAA+09CT..."}', $historyContainer[0]['request']->getBody()->getContents());
    }

    public function testFetch()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], file_get_contents(__dir__ . '/data/release_fetch.json')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);
        $client = new Client(['handler' => $handlerStack]);

        $release = new Release('puppetlabs-apache-4.0.0', $client);
        $result = $release->fetch();

        $this->assertEquals('releases/puppetlabs-apache-4.0.0', $historyContainer[0]['request']->getUri());
        $this->assertEquals('puppetlabs-apache-4.0.0', $result->slug);
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

        $release = new Release('puppetlabs-apache-4.0.0', $client);
        $result = $release->delete('bugs');
        $this->assertTrue($result);
        $event = $historyContainer[0];
        $this->assertEquals('DELETE', $event['request']->getMethod());
        $this->assertEquals('reason=bugs', $event['request']->getUri()->getQuery());

        $this->assertFalse($release->delete('bugs'));
    }
}
