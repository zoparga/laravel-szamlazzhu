<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class XmlReadingException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 *
 * The request XML could be read. Maybe malformed or wrongly formatted
 */
class XmlReadingException extends ClientException
{

    protected $code = 57;

    protected $message = 'XML reading error.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'There is an error in the sent XML file. The response body contains more information.';
    }
}