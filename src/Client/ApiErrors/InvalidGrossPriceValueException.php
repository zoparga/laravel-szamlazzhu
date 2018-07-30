<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


class InvalidGrossPriceValueException extends ClientException
{

    protected $code = 261;

    protected $message = 'The gross value of the item is not correct; gross value = NET value of item + VAT value of item. Product:';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'Your values don’t fit into this formula, please verify and correct the gross value. The response body contains the name of the wrong product after the “Termék” word.';
    }
}