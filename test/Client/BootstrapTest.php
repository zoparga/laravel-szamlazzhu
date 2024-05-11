<?php

namespace zoparga\SzamlazzHu\Tests\Client;


use zoparga\SzamlazzHu\Client\Client;
use zoparga\SzamlazzHu\Client\Errors\InvalidClientConfigurationException;

class BootstrapTest extends TestCase
{

    protected $guzzle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guzzle = new \GuzzleHttp\Client();
    }


    public function test_can_initialize_with_api_key_given_only()
    {

        new Client([
            'credentials' => [
                'api_key' => 'foo',
            ]
        ], $this->guzzle);

        $this->assertTrue(true);
    }

}
