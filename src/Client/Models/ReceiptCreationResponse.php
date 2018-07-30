<?php


namespace SzuniSoft\SzamlazzHu\Client\Models;
use Carbon\Carbon;

/**
 * Class ReceiptCreationResponse
 * @package SzuniSoft\SzamlazzHu\Client\Models
 *
 * [Attributes]
 * @property-read int $id
 * @property-read string $callId
 * @property-read string $receiptNumber
 * @property-read Carbon $createdAt
 * @property-read boolean $isCancelled
 * @property-read string $pdfBase64
 *
 */
class ReceiptCreationResponse extends CommonResponseModel {

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
            'callId' => $content['nyugta']['alap']['hivasAzonosito'],
            'receiptNumber' => $content['nyugta']['alap']['nyugtaszam'],
            'createdAt' => Carbon::createFromFormat('Y-m-d',$content['nyugta']['alap']['kelt']),
            'isCancelled' => $content['nyugta']['alap']['stornozott'] !== 'false',
            'pdfBase64' => isset($content['nyugtaPdf']) ? $content['nyugtaPdf'] : null
        ];
    }
}