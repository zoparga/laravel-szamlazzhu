<?php

namespace zoparga\SzamlazzHu\Tests\Client\Fixtures;

use GuzzleHttp\Psr7\Response;

class QueryTaxPayerErrorResponse extends Response
{
    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<QueryTaxpayerResponse xmlns="http://schemas.nav.gov.hu/OSA/2.0/api" xmlns:ns2="http://schemas.nav.gov.hu/OSA/2.0/data">
    <header>
        <requestId>-</requestId>
        <timestamp>2020-11-04T11:31:27.122Z</timestamp>
        <requestVersion>2.0</requestVersion>
    </header>
    <result>
        <funcCode>ERROR</funcCode>
        <errorCode>57</errorCode>
        <message>XML beolvasási hiba. cvc-pattern-valid: Value \'1342173\' is not facet-valid with respect to pattern \'[0-9]{8}\' for type \'torszszamTipus\'.</message>
    </result>
</QueryTaxpayerResponse>';

        parent::__construct($status, $headers, $body, $version, $reason);
    }
}