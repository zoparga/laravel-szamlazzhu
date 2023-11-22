<?php


namespace zoparga\SzamlazzHu\Client\Models;

use zoparga\SzamlazzHu\Client\Client;


/**
 * Class Invoice
 * @package zoparga\SzamlazzHu\Client\Models
 *
 * [Attributes]
 * @property-read string $invoiceNumber
 * @property-read string $netPrice
 * @property-read string $grossPrice
 * @property-read string $paymentUrl
 * @property-read string $pdfBase64
 *
 * Abstraction of remotely obtained invoice preview.
 */
class InvoicePreviewResponse extends CommonResponseModel
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
            'netPrice' => $content['szamlanetto'],
            'grossPrice' => $content['szamlabrutto'],
            'pdfBase64' => isset($content['pdf']) ? $content['pdf'] : null,
        ];
    }
}
