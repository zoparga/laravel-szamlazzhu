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
 * @property-read string|null $countyCode
 * @property-read bool $taxpayerValidity
 * @property-read string|null $taxpayerName
 * @property-read string|null $incorporation
 * @property-read string|null $errorMessage
 */
class QueryTaxPayerResponse extends CommonResponseModel
{
    public function __construct(Client $client, ResponseInterface $response)
    {
        parent::__construct($client, $response);
    }

    /**
     * @param \SimpleXMLElement $content
     * @return array
     * @throws \DateMalformedStringException
     */
    protected function mapAttributes($content)
    {
        if ($content->result->funcCode->__toString() === 'ERROR') {
            return [
                'errorMessage' => sprintf('[%s] %s', $content->result->errorCode->__toString(), $content->result->message->__toString()),
                'infoDate' => null,
                'taxpayerId' => null,
                'vatCode' => null,
                'countyCode' => null,
                'taxpayerValidity' => false,
                'taxpayerName' => null,
                'incorporation' => null,
            ];

        }
        return [
            'infoDate' => isset($content->infoDate) ? new \DateTime($content->infoDate->__toString()) : null,
            'taxpayerId' => isset($content->taxpayerData->taxNumberDetail->taxpayerId) ? $content->taxpayerData->taxNumberDetail->taxpayerId->__toString() : null,
            'vatCode' => isset($content->taxpayerData->taxNumberDetail->vatCode) ? $content->taxpayerData->taxNumberDetail->vatCode->__toString() : null,
            'countyCode' => isset($content->taxpayerData->taxNumberDetail->countyCode) ? $content->taxpayerData->taxNumberDetail->vatCode->__toString() : null,
            'taxpayerValidity' => isset($content->taxpayerValidity) && $content->taxpayerValidity->__toString() === 'true',
            'taxpayerName' => isset($content->taxpayerData->taxpayerShortName) ? $content->taxpayerData->taxpayerShortName->__toString() : null,
            'incorporation' => isset($content->taxpayerData->incorporation) ? $content->taxpayerData->incorporation->__toString() : null,
            'errorMessage' => null,
        ];
    }

    public function parse($payload)
    {
        $xml = simplexml_load_string($payload, 'SimpleXMLElement', (LIBXML_VERSION >= 20700) ? (LIBXML_PARSEHUGE | LIBXML_NOCDATA) : LIBXML_NOCDATA);
        return $this->removeNamespaces($xml);
    }

    private function removeNamespaces(\SimpleXMLElement $xml) {

        while($namespaces = $xml->getDocNamespaces(true, true)) {

            $uri    = reset($namespaces);
            $prefix = key($namespaces);

            $elements = $xml->xpath("//*[namespace::*[name() = '{$prefix}' and . = '{$uri}'] and not (../namespace::*[name() = '{$prefix}' and . = '{$uri}'])]");
            $element  = dom_import_simplexml($elements[0]);

            foreach($namespaces as $prefix => $uri) {
                $element->removeAttributeNS($uri, $prefix);
            }

            $xml = new \SimpleXMLElement($xml->asXML());
        }

        return $xml;
    }
}
