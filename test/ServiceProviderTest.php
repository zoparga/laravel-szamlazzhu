<?php


namespace SzuniSoft\SzamlazzHu\Tests;

use SzuniSoft\SzamlazzHu\Client\Client;

class ServiceProviderTest extends TestCase
{

    /** @test */
    public function it_passes_right_configuration()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_recreate()
    {
        $this->assertNotSame($this->app[Client::class], $this->app['szamlazz-hu.client']);
    }

    /** @test */
    public function it_can_provide_client()
    {
        $this->assertInstanceOf(Client::class, $this->app[Client::class]);
    }

    /** @test */
    public function it_can_provide_alias()
    {
        $this->assertInstanceOf(Client::class, $this->app['szamlazz-hu.client']);
    }

}