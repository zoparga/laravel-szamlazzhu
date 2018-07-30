<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client\Fixtures;


use GuzzleHttp\Psr7\Response;

class InvoiceCreationResponse extends Response {

    /**
     * InvoiceCreationResponse constructor.
     * @param $successful
     * @param $invoiceNumber
     * @param $netPrice
     * @param $grossPrice
     * @param $paymentUrl
     * @param $pdf
     * @param int $status
     * @param array $headers
     * @param null $body
     * @param string $version
     * @param null|string $reason
     */
    public function __construct(
        $successful,
        $invoiceNumber,
        $netPrice,
        $grossPrice,
        $paymentUrl,
        $pdf,
        int $status = 200, array $headers = [], $body = null, string $version = '1.1', ?string $reason = null)
    {

        $body = $body ?: '<?xml version="1.0" encoding="UTF-8"?>
            <xmlszamlavalasz xmlns="http://www.szamlazz.hu/xmlszamlavalasz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <sikeres>'. ($successful ? "true" : "false") .'</sikeres>
                <szamlaszam>' . $invoiceNumber .'</szamlaszam>
                <szamlanetto>'. $netPrice .'</szamlanetto>
                <szamlabrutto>'. $grossPrice .'</szamlabrutto>
                <vevoifiokurl>'. $paymentUrl .'</vevoifiokurl>
                <pdf>'. base64_encode($pdf) .'</pdf>
            </xmlszamlavalasz>
       ';

        parent::__construct($status, $headers, $body, $version, $reason);
    }


}