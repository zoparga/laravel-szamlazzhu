<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;


use SzuniSoft\SzamlazzHu\Client\Client;

/**
 * Trait ClientAccessor
 * @package SzuniSoft\SzamlazzHu\Support
 */
trait ClientAccessor
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client ?: app('szamlazz-hu.client');
    }

}