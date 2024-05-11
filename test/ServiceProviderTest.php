<?php


namespace zoparga\SzamlazzHu\Tests;

use zoparga\SzamlazzHu\Client\Client;

class ServiceProviderTest extends TestCase
{


    public function test_it_passes_right_configuration()
    {
        $this->assertTrue(true);
    }


    public function test_it_can_recreate()
    {
        $this->assertNotSame($this->app[Client::class], $this->app['szamlazz-hu.client']);
    }


    public function test_it_can_provide_client()
    {
        $this->assertInstanceOf(Client::class, $this->app[Client::class]);
    }


    public function test_it_can_provide_alias()
    {
        $this->assertInstanceOf(Client::class, $this->app['szamlazz-hu.client']);
    }

}
