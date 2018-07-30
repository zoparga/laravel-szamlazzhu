<?php


namespace SzuniSoft\SzamlazzHu\Client\Models;

use SzuniSoft\SzamlazzHu\Client\Client;


/**
 * Class Invoice
 * @package SzuniSoft\SzamlazzHu\Client\Models
 *
 * [Attributes]
 * @property-read string $invoiceNumber
 * @property-read string $netPrice
 * @property-read string $grossPrice
 * @property-read string $paymentUrl
 * @property-read string $pdfBase64
 *
 * Abstraction of remotely obtained invoice.
 */
class InvoiceCreationResponse extends CommonResponseModel
{

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var Client
     */
    protected $client;


    /**
     * Maps remote attributes
     *
     * @param array |string $content
     * @return array
     */
    protected function mapAttributes($content)
    {
        return [
            'invoiceNumber' => $content['szamlaszam'],
            'netPrice' => $content['szamlanetto'],
            'grossPrice' => $content['szamlabrutto'],
            'paymentUrl' => isset($content['vevoifiokurl']) ? $content['vevoifiokurl'] : null,
            'pdfBase64' => isset($content['pdf']) ? $content['pdf'] : null,
        ];
    }
}