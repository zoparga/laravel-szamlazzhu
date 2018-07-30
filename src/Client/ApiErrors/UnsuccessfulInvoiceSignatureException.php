<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class UnsuccessfulInvoiceSignatureException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 *
 * The invoice signing was not successful
 */
class UnsuccessfulInvoiceSignatureException extends ClientException
{

    protected $code = 55;

    protected $message = 'E-invoice signature was unsuccessful.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'Either your certificate expired or timestamp server could not be reached.';
    }
}