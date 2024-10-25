<?php

namespace zoparga\SzamlazzHu\Tests\Client\TaxPayer;

use zoparga\SzamlazzHu\Tests\Client\Fixtures\QueryTaxPayerErrorResponse;
use zoparga\SzamlazzHu\Tests\Client\Fixtures\QueryTaxPayerInvalidResponse;
use zoparga\SzamlazzHu\Tests\Client\Fixtures\QueryTaxPayerResponse;

class TaxPayerTest extends TestCase
{
    public function test_it_can_query_tax_payer()
    {
        $client = $this->client(new QueryTaxPayerResponse());
        $response = $client->queryTaxPayer('13421739-1-42');
        $this->assertTrue($response->taxpayerValidity);
        $this->assertNotNull($response->taxpayerId);
        $this->assertNotNull($response->vatCode);
        $this->assertNotNull($response->infoDate);
        $this->assertNotNull($response->taxpayerName);
        $this->assertNull($response->errorMessage);
    }

    public function test_it_can_query_invalid_tax_payer()
    {
        $client = $this->client(new QueryTaxPayerInvalidResponse());
        $response = $client->queryTaxPayer('134217');
        $this->assertFalse($response->taxpayerValidity);
        $this->assertNull($response->taxpayerId);
        $this->assertNull($response->vatCode);
        $this->assertNull($response->infoDate);
        $this->assertNull($response->taxpayerName);
        $this->assertNull($response->errorMessage);
    }

    public function test_it_can_query_tax_payer_and_fail()
    {
        $client = $this->client(new QueryTaxPayerErrorResponse());
        $response = $client->queryTaxPayer('134217');
        $this->assertFalse($response->taxpayerValidity);
        $this->assertNull($response->taxpayerId);
        $this->assertNull($response->vatCode);
        $this->assertNull($response->infoDate);
        $this->assertNull($response->taxpayerName);
        $this->assertNotNull($response->errorMessage);
    }
}