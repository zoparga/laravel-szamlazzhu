<?php

namespace zoparga\SzamlazzHu\Tests\Client\Fixtures;

use GuzzleHttp\Psr7\Response;

class QueryTaxPayerResponse extends Response
{
    public function __construct($taxId = 13421739, int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?><QueryTaxpayerResponse xmlns="http://schemas.nav.gov.hu/OSA/3.0/api" xmlns:ns2="http://schemas.nav.gov.hu/OSA/3.0/base" xmlns:ns3="http://schemas.nav.gov.hu/NTCA/1.0/common" xmlns:ns4="http://schemas.nav.gov.hu/OSA/3.0/data">
    <ns3:header>
        <ns3:requestId>36209_dgt7jaenryrqpacdzzenry</ns3:requestId>
        <ns3:timestamp>2024-10-25T11:38:18.590Z</ns3:timestamp>
        <ns3:requestVersion>3.0</ns3:requestVersion>
        <ns3:headerVersion>1.0</ns3:headerVersion>
    </ns3:header>
    <ns3:result>
        <ns3:funcCode>OK</ns3:funcCode>
    </ns3:result>
    <software>
        <softwareId>SZAMLAZZHU34540973</softwareId>
        <softwareName>Számlázz.hu</softwareName>
        <softwareOperation>ONLINE_SERVICE</softwareOperation>
        <softwareMainVersion>v20241025</softwareMainVersion>
        <softwareDevName>Számlázz.hu szolgáltatásfejlesztés</softwareDevName>
        <softwareDevContact>fejlesztes@szamlazz.hu</softwareDevContact>
    </software>
    <infoDate>2017-09-06T00:00:00.000+02:00</infoDate>
    <taxpayerValidity>true</taxpayerValidity>
    <taxpayerData>
        <taxpayerName>KBOSS.HU KERESKEDELMI ÉS SZOLGÁLTATÓ KORLÁTOLT FELELŐSSÉGŰ TÁRSASÁG</taxpayerName>
        <taxpayerShortName>KBOSS.HU KFT.</taxpayerShortName>
        <taxNumberDetail>
            <ns2:taxpayerId>13421739</ns2:taxpayerId>
            <ns2:vatCode>2</ns2:vatCode>
            <ns2:countyCode>41</ns2:countyCode>
        </taxNumberDetail>
        <incorporation>ORGANIZATION</incorporation>
        <taxpayerAddressList>
            <taxpayerAddressItem>
                <taxpayerAddressType>HQ</taxpayerAddressType>
                <taxpayerAddress>
                    <ns2:countryCode>HU</ns2:countryCode>
                    <ns2:postalCode>1031</ns2:postalCode>
                    <ns2:city>BUDAPEST</ns2:city>
                    <ns2:streetName>ZÁHONY</ns2:streetName>
                    <ns2:publicPlaceCategory>UTCA</ns2:publicPlaceCategory>
                    <ns2:number>7.</ns2:number>
                </taxpayerAddress>
            </taxpayerAddressItem>
        </taxpayerAddressList>
    </taxpayerData>
</QueryTaxpayerResponse>
';

        parent::__construct($status, $headers, $body, $version, $reason);
    }
}