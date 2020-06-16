<?php


namespace SzuniSoft\SzamlazzHu\Client\Models;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\Util\XmlParser;


/**
 * Class CommonResponseModel
 * @package SzuniSoft\SzamlazzHu\Client\Models
 */
abstract class CommonResponseModel
{

    use XmlParser;

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
     *
     * @param Client            $client
     * @param ResponseInterface $response
     */
    public function __construct(Client $client, ResponseInterface $response)
    {
        $content      = (string)$response->getBody();
        $this->client = $client;

        try {
            $this->attributes = $this->mapAttributes(
                static::$noXml ? $content : $this->parse($content)
            );
        }
        catch (RuntimeException $e) {

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
     *
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
     *
     * @return array
     */
    abstract protected function mapAttributes($content);

}
