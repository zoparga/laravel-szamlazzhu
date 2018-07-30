<?php


namespace SzuniSoft\SzamlazzHu\Client\Models;

use Nathanmac\Utilities\Parser\Exceptions\ParserException;
use Nathanmac\Utilities\Parser\Parser;
use Psr\Http\Message\ResponseInterface;
use SzuniSoft\SzamlazzHu\Client\Client;


/**
 * Class CommonResponseModel
 * @package SzuniSoft\SzamlazzHu\Client\Models
 */
abstract class CommonResponseModel {

    protected static $noXml = false;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var Client
     */
    protected $client;

    /**
     * CommonResponseModel constructor.
     * @param Client $client
     * @param ResponseInterface $response
     */
    public function __construct(Client $client, ResponseInterface $response)
    {
        $content = (string)$response->getBody();
        $this->client = $client;

        try {
            $this->attributes = $this->mapAttributes(
                static::$noXml ? $content : (new Parser)->xml($content)
            );
        } catch (ParserException $e) {

        }
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * Maps remote attributes
     *
     * @param array|string $content
     * @return array
     */
    abstract protected function mapAttributes($content);

}