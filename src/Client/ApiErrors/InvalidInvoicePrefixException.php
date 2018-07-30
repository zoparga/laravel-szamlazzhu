<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class InvalidInvoicePrefixException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 */
class InvalidInvoicePrefixException extends ClientException
{

    protected $code = 202;

    protected $message = 'The given invoice prefix is not usable. You can only use those prefixes for the Invoice Agent that were previously registered in Szamlazz.hu’s system.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'You need to login to the website and add the desired prefixes before you could use them with the Invoice Agent.';
    }
}