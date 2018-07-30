<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client;


use GuzzleHttp\Handler\MockHandler;
use SzuniSoft\SzamlazzHu\Client\Errors\InvalidClientConfigurationException;

class TestCase extends \Orchestra\Testbench\TestCase {

    /**
     * @return array
     */
    protected function merchant()
    {
        return [
            'bank' => 'bank',
            'bankAccountNumber' => '123',
            'replyEmail' => 'bank@bank.bank'
        ];
    }

    /**
     * @return array
     */
    protected function customer()
    {
        return [
            'customerName' => 'customer',
            'customerZipCode' => 'Foreign zip code isles',
            'customerCity' => 'Deep dungeons',
            'customerAddress' => 'Level #5',
        ];
    }

    /**
     * @param mixed $stack
     * @param array $config
     * @param array $merchant
     * @return \SzuniSoft\SzamlazzHu\Client\Client
     */
    protected function client($stack = [], $config = [], $merchant = [])
    {

        if (!is_array($stack)) {
            $stack = [$stack];
        }

        try {
            return new \SzuniSoft\SzamlazzHu\Client\Client(
                array_merge(
                    [
                        'credentials' => [
                            'username' => 'test',
                            'password' => 'test'
                        ],
                        'certificate' => [
                            'enabled' => false
                        ],
                    ],
                    $config
                ),
                new \GuzzleHttp\Client(['handler' => new MockHandler($stack)]),
                $merchant
                );
        } catch (InvalidClientConfigurationException $e) {
            return null;
        }
    }

}