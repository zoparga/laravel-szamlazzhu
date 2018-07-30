<?php


namespace SzuniSoft\SzamlazzHu\Client\Models;


use Psr\Http\Message\ResponseInterface;
use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\ProformaInvoice;

class ProformaInvoiceDeletionResponse extends CommonResponseModel
{

    protected static $noXml = true;

    /**
     * @var ProformaInvoice
     */
    public $originalProformaInvoice;

    /**
     * ProformaInvoiceDeletionResponse constructor.
     * @param ProformaInvoice $originalInvoice
     * @param Client $client
     * @param ResponseInterface $response
     */
    public function __construct(ProformaInvoice $originalInvoice, Client $client, ResponseInterface $response)
    {
        parent::__construct($client, $response);
        $this->originalProformaInvoice = $originalInvoice;
    }


    /**
     * Maps remote attributes
     *
     * @param array|string $content
     * @return array
     */
    protected function mapAttributes($content)
    {
        return [];
    }
}