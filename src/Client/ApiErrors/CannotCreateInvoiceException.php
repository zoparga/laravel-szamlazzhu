<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class CannotCreateInvoiceException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 *
 * Invoice could not be created because of authorization problems
 */
class CannotCreateInvoiceException extends ClientException
{

    protected $code = 54;

    protected $message = 'E-invoice generation is not permitted.';

    /**
     * @return string
     */
    function getInfo()
    {
        return 'Probably your subscription package does not contain it, or there is no own certificate uploaded, in such case you have to accept that KBOSS.hu Kft. Can use its own certificate for the invoices.';
    }
}