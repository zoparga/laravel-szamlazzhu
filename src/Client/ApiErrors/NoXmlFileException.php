<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class NoXmlFileException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 *
 * No XML file was provided in request body
 */
class NoXmlFileException extends ClientException
{

    protected $code = 53;

    protected $message = 'Missing XML file.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'Probably the XML was not sent as file, but as an input content of an HTML.';
    }
}