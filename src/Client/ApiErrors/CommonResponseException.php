<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class CommonException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 *
 * Should be not dedicated error when remote error answer
 * does not contain error code or it does but unknown.
 */
class CommonResponseException extends ClientException
{

    protected $info;

    /**
     * CommonException constructor.
     * @param ResponseInterface $response
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(ResponseInterface $response, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->info = $message;
        parent::__construct($response, $message, $code, $previous);
    }


    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return $this->info;
    }
}