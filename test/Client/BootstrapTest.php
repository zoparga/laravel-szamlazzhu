<?php

namespace SzuniSoft\SzamlazzHu\Tests\Client;


use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\Client\Errors\InvalidClientConfigurationException;

class BootstrapTest extends TestCase
{

    protected $guzzle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guzzle = new \GuzzleHttp\Client();
    }

    /** @test */
    public function can_initialize_with_api_key_given_only()
    {

        new Client([
            'credentials' => [
                'api_key' => 'foo',
            ]
        ], $this->guzzle);

        $this->assertTrue(true);
    }

    /** @test */
    function initializes_when_cert_specified_but_enabled()
    {

        new Client([
            'credentials' => [
                'username' => 'test',
                'password' => 'test'
            ],
            'certificate' => [
                'enabled' => false,
                'path' => '/test.pem'
            ]
        ], $this->guzzle);

        $this->assertTrue(true);
    }

    /** @test */
    function fails_when_enabled_but_disk_not_provided()
    {

        $this->expectException(InvalidClientConfigurationException::class);

        new Client([
            'credentials' => [
                'username' => 'test',
                'password' => 'test'
            ],
            'certificate' => [
                'enabled' => true,
                'path' => '/test.pem'
            ]
        ], $this->guzzle);
    }

}
