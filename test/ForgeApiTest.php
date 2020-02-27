<?php

namespace Edu\Iu\Uits\Webtech\ForgeApi\Test;

use Edu\Iu\Uits\Webtech\ForgeApi\ForgeApi;
use PHPUnit\Framework\TestCase;

/**
 * Class ForgeApiTest
 * @package Edu\Iu\Uits\Webtech\ForgeApi\Test
 */
class ForgeApiTest extends TestCase
{

    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Edu\Iu\Uits\Webtech\ForgeApi\ForgeApi',
            new ForgeApi('test')
        );
    }

    public function testUser()
    {
        $api = new ForgeApi('test');
        $this->assertInstanceOf(
            '\Edu\Iu\Uits\Webtech\ForgeApi\User',
            $api->user()
        );
    }

    public function testModule()
    {
        $api = new ForgeApi('test');
        $this->assertInstanceOf(
            '\Edu\Iu\Uits\Webtech\ForgeApi\Module',
            $api->module()
        );
    }

    public function testRelease()
    {
        $api = new ForgeApi('test');
        $this->assertInstanceOf(
            '\Edu\Iu\Uits\Webtech\ForgeApi\Release',
            $api->release()
        );
    }
}
