<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class KeystoreOpeningException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 *
 * Request could not open the keystore.
 */
class KeystoreOpeningException extends ClientException
{

    protected $code = 49;

    protected $message = 'To create an electronic invoice, please give your secret password which opens the keystore.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'To generate the invoice please provide the secret password for key';
    }
}