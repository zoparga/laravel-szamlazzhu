<?php

namespace zoparga\SzamlazzHu\Tests\Client\Fixtures;

use GuzzleHttp\Psr7\Response;

class QueryTaxPayerInvalidResponse extends Response
{
    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?><ns2:QueryTaxpayerResponse xmlns:ns2="http://schemas.nav.gov.hu/OSA/3.0/api" xmlns="http://schemas.nav.gov.hu/NTCA/1.0/common" xmlns:ns3="http://schemas.nav.gov.hu/OSA/3.0/base" xmlns:ns4="http://schemas.nav.gov.hu/OSA/3.0/data">
    <header>
        <requestId>36209_iqa5qxsjbkaygxe5xihuzr</requestId>
        <timestamp>2024-10-25T11:35:56.584Z</timestamp>
        <requestVersion>3.0</requestVersion>
        <headerVersion>1.0</headerVersion>
    </header>
    <result>
        <funcCode>OK</funcCode>
    </result>
    <ns2:software>
        <ns2:softwareId>SZAMLAZZHU34540973</ns2:softwareId>
        <ns2:softwareName>Számlázz.hu</ns2:softwareName>
        <ns2:softwareOperation>ONLINE_SERVICE</ns2:softwareOperation>
        <ns2:softwareMainVersion>v20241025</ns2:softwareMainVersion>
        <ns2:softwareDevName>Számlázz.hu szolgáltatásfejlesztés</ns2:softwareDevName>
        <ns2:softwareDevContact>fejlesztes@szamlazz.hu</ns2:softwareDevContact>
    </ns2:software>
    <ns2:taxpayerValidity>false</ns2:taxpayerValidity>
</ns2:QueryTaxpayerResponse>
';

        parent::__construct($status, $headers, $body, $version, $reason);
    }
}