<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


use Exception;
use Psr\Http\Message\ResponseInterface;
use Throwable;

abstract class ClientException extends Exception
{

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * AbstractClientException constructor.
     * @param ResponseInterface $response
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(ResponseInterface $response, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }


    /**
     * More detailed info of exception.
     *
     * @return string
     */
    abstract function getInfo();

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

}