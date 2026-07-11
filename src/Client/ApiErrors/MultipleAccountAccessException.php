<?php


namespace zoparga\SzamlazzHu\Client\ApiErrors;


/**
 * Class MultipleAccountAccessException
 * @package zoparga\SzamlazzHu\Client\ApiErrors
 *
 * The authenticated user has access to more than one Szamlazz.hu billing account
 */
class MultipleAccountAccessException extends ClientException
{

    protected $code = 164;

    protected $message = 'This function can only be used by a user who has access to a single Szamlazz.hu account.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'The given user has access to more than one billing account, so this function cannot be used. Generate an Agent key and use it for authentication instead, to avoid the authentication problem caused by multiple account access.';
    }
}
