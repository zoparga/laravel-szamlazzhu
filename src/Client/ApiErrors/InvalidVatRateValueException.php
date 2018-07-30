<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class InvalidVatRateValueException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 */
class InvalidVatRateValueException extends ClientException
{

    protected $code = 260;

    protected $message = 'The VAT value of the item is not correct; VAT value = NET price of item * VAT rate / 100. Product:';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'Your values don’t fit into this formula, please verify and correct the VAT value. The response body contains the name of the wrong product after the “Termék” word.';
    }
}