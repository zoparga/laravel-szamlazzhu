<?php


namespace zoparga\SzamlazzHu\Client\ApiErrors;


/**
 * Class AuthenticationException
 * @package zoparga\SzamlazzHu\Client\ApiErrors
 *
 * The provided login credentials are invalid
 */
class AuthenticationException extends ClientException
{

    protected $code = 2;

    protected $message = 'Login error';

    /**
     * @return string
     */
    function getInfo()
    {
        return 'Provided login name and password not accepted.';
    }
}
