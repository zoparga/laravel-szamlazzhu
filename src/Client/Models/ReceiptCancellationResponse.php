<?php

namespace SzuniSoft\SzamlazzHu\Client\Models;
use Psr\Http\Message\ResponseInterface;
use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\Receipt;


/**
 * Class ReceiptCancellationResponse
 * @package SzuniSoft\SzamlazzHu\Client\Models
 *
 * @property-read int $id
 * @property-read string $newReceiptNumber
 * @property-read string $originalReceiptNumber
 * @property-read string $pdfBase64
 */
class ReceiptCancellationResponse extends CommonResponseModel {

    /**
     * @var Receipt
     */
    protected $originalReceipt;

    /**
     * @var Receipt|null
     */
    protected $cancellationReceipt = null;

    public function __construct(Receipt $originalReceipt, Client $client, ResponseInterface $response)
    {
        $this->originalReceipt = $originalReceipt;
        parent::__construct($client, $response);
    }

    /**
     * @return Receipt|null
     */
    public function cancellationReceipt()
    {
        return $this->cancellationReceipt ?: ($this->cancellationReceipt = $this->client->getReceiptByReceiptNumber($this->newReceiptNumber));
    }

    /**
     * @return Receipt
     */
    public function originalReceipt()
    {
        return $this->originalReceipt;
    }

    /**
     * Maps remote attributes
     *
     * @param array|string $content
     * @return array
     */
    protected function mapAttributes($content)
    {
        return [
            'id' => $content['nyugta']['alap']['id'],
            'newReceiptNumber' => $content['nyugta']['alap']['nyugtaszam'],
            'originalReceiptNumber' => $content['nyugta']['alap']['stornozottNyugtaszam'],
            'isCancelled' => true,
            'pdfBase64' => isset($content['nyugtaPdf']) ? $content['nyugtaPdf'] : null
        ];
    }
}