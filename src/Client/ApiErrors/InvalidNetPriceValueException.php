<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class InvalidNetPriceValueException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 */
class InvalidNetPriceValueException extends ClientException
{

    protected $code = 259;

    protected $message = 'The NET value of the item is not correct; NET value = NET unit price * quantity. Product:';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'Your values don’t fit into this formula, please verify and correct the NET value. The response body contains the name of the wrong product after the “Termék” word.';
    }
}