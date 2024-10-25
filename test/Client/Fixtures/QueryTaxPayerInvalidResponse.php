<?php

namespace zoparga\SzamlazzHu\Tests\Client\Fixtures;

use GuzzleHttp\Psr7\Response;

class QueryTaxPayerInvalidResponse extends Response
{
    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?>
<QueryTaxpayerResponse xmlns="http://schemas.nav.gov.hu/OSA/2.0/api" xmlns:ns2="http://schemas.nav.gov.hu/OSA/2.0/data">
   <header>
      <requestId>38046_g2z6726bg67ymdt3p56bg6</requestId>
      <timestamp>2020-11-04T11:29:40.656Z</timestamp>
      <requestVersion>2.0</requestVersion>
   </header>
   <result>
      <funcCode>OK</funcCode>
   </result>
   <taxpayerValidity>false</taxpayerValidity>
</QueryTaxpayerResponse>';

        parent::__construct($status, $headers, $body, $version, $reason);
    }
}