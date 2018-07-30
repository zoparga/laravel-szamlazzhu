<?php


namespace SzuniSoft\SzamlazzHu\Client\Errors;


use Exception;
use \Illuminate\Contracts\Validation\Validator;
use Throwable;

/**
 * Class InvalidClientConfigurationException
 * @package SzuniSoft\SzamlazzHu\Client\Errors
 *
 * Should be triggered when the client configuration is invalid
 */
class InvalidClientConfigurationException extends Exception
{

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(Validator $validator, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->validator = $validator;
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    public function __toString()
    {
        return implode(',', $this->validator->getMessageBag()->getMessages());
    }


}