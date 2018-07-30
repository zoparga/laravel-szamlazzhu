<?php


namespace SzuniSoft\SzamlazzHu\Client\Models;

use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\Invoice;


/**
 * Class InvoiceCancellationResponse
 *
 * [Attributes]
 * @property-read string $cancellationInvoiceNumber
 *
 * @package SzuniSoft\SzamlazzHu\Client\Models
 */
class InvoiceCancellationResponse extends CommonResponseModel {

    protected static $noXml = true;

    /**
     * @var Invoice
     */
    public $originalInvoice;

    /**
     * @var Invoice
     */
    protected $cancellationInvoice = null;

    /**
     * InvoiceCancellationResponse constructor.
     * @param Invoice $originalInvoice
     * @param Client $client
     * @param ResponseInterface $response
     */
    public function __construct(Invoice $originalInvoice, Client $client, ResponseInterface $response)
    {
        parent::__construct($client, $response);
        $this->originalInvoice = $originalInvoice;
    }

    /**
     * @return Invoice
     */
    public function originalInvoice()
    {
        return $this->originalInvoice;
    }

    /**
     * @return Invoice|null
     * @throws \SzuniSoft\SzamlazzHu\Client\ApiErrors\CommonResponseException
     */
    public function cancellationInvoice()
    {
        if (!$this->cancellationInvoiceNumber) {
            return null;
        }
        return $this->cancellationInvoice ?: ($this->cancellationInvoice = $this->client->getInvoice($this->cancellationInvoiceNumber));
    }

    /**
     * Maps remote attributes
     *
     * @param array|string $content
     * @return array
     */
    protected function mapAttributes($content)
    {
        if (Str::contains($content, ';')) {
            return [
                'cancellationInvoiceNumber' => str_replace("\n", "", explode(';', $content, 2)[1])
            ];
        }

        return [];
    }
}