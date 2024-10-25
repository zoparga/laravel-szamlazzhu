<?php

namespace zoparga\SzamlazzHu\Tests\Client\Fixtures;

use GuzzleHttp\Psr7\Response;

class QueryTaxPayerResponse extends Response
{
    public function __construct($taxId = 13421739, int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?>
<QueryTaxpayerResponse xmlns="http://schemas.nav.gov.hu/OSA/2.0/api" xmlns:ns2="http://schemas.nav.gov.hu/OSA/2.0/data">
   <header>
      <requestId>38046_g2z6726bg67ymdt3p56bg6</requestId>
      <timestamp>2020-11-04T11:26:50.456Z</timestamp>
      <requestVersion>2.0</requestVersion>
   </header>
   <result>
      <funcCode>OK</funcCode>
   </result>
   <software>
      <softwareId>SZAMLAZZHU34540973</softwareId>
      <softwareName>Számlázz.hu</softwareName>
      <softwareOperation>ONLINE_SERVICE</softwareOperation>
      <softwareMainVersion>v20201104</softwareMainVersion>
      <softwareDevName>Számlázz.hu szolgáltatásfejlesztés</softwareDevName>
      <softwareDevContact>fejlesztes@szamlazz.hu</softwareDevContact>
   </software>
   <infoDate>2004-12-26T23:00:00.000Z</infoDate>
   <taxpayerValidity>true</taxpayerValidity>
   <taxpayerData>
      <taxpayerName>KBOSS.HU KERESKEDELMI ÉS SZOLGÁLTATÓ KORLÁTOLT FELELŐSSÉGŰ TÁRSASÁG</taxpayerName>
      <taxNumberDetail>
         <ns2:taxpayerId>'.$taxId.'</ns2:taxpayerId>
         <ns2:vatCode>2</ns2:vatCode>
      </taxNumberDetail>
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
</QueryTaxpayerResponse>';

        parent::__construct($status, $headers, $body, $version, $reason);
    }
}