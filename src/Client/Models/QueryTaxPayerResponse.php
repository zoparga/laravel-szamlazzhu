<?php

namespace zoparga\SzamlazzHu\Client\Models;

use Psr\Http\Message\ResponseInterface;
use zoparga\SzamlazzHu\Client\Client;

/**
 * Class QueryTaxPayerResponse
 * @author zdaniel
 * @package zoparga\SzamlazzHu\Client\Models
 * @property-read \DateTimeInterface|null $infoDate
 * @property-read string|null $taxpayerId
 * @property-read string|null $vatCode
 * @property-read bool $taxpayerValidity
 * @property-read string|null $taxpayerName
 * @property-read string|null $errorMessage
 */
class QueryTaxPayerResponse extends CommonResponseModel
{
    public function __construct(Client $client, ResponseInterface $response)
    {
        parent::__construct($client, $response);
    }

    protected function mapAttributes($content)
    {
        if ($content['result']['funcCode'] === 'ERROR') {
            return [
                'errorMessage' => sprintf('[%s] %s',$content['result']['errorCode'], $content['result']['message']),
                'infoDate' => null,
                'taxpayerId' => null,
                'vatCode' => null,
                'taxpayerValidity' => false,
                'taxpayerName' => null,
            ];

        }
        return [
            'infoDate' => isset($content['infoDate']) ? new \DateTime($content['infoDate']) : null,
            'taxpayerId' => $content['taxpayerData']['taxNumberDetail']['ns2:taxpayerId'] ?? null,
            'vatCode' => $content['taxpayerData']['taxNumberDetail']['ns2:vatCode'] ?? null,
            'taxpayerValidity' => $content['taxpayerValidity'] === 'true',
            'taxpayerName' => $content['taxpayerData']['taxpayerName'] ?? null,
            'errorMessage' => null,
        ];
    }
}