<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client\Fixtures;


use GuzzleHttp\Psr7\Response;

class ProformaInvoiceDeletionResponse extends Response {

    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?>
                    <xmlszamladbkdelvalasz​ xmlns="http://www.szamlazz.hu/xmlszamladbkdelvalasz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.szamlazz.hu/xmlszamladbkdelvalasz http://www.szamlazz.hu/docs/xsds/szamladbkdel/xmlszamladbkdelvalasz.xsd ">
                        <sikeres>true</sikeres>
                    </xmlszamladbkdelvalasz>
                    ';

        parent::__construct($status, $headers, $body, $version, $reason);
    }


}