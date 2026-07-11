<?php


namespace zoparga\SzamlazzHu\Client\ApiErrors;


/**
 * Class BrowserSessionConflictException
 * @package zoparga\SzamlazzHu\Client\ApiErrors
 *
 * The Agent user is also logged in to Szamlazz.hu through a browser session
 */
class BrowserSessionConflictException extends ClientException
{

    protected $code = 135;

    protected $message = 'Please log out of Szamlazz.hu in your browser to run the Invoice Agent.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'If the Agent user is logged into Szamlazz.hu in a browser window, while the XML request is being sent from elsewhere (e.g. a test HTML file or a curl POST request), it can cause errors on the Szamlazz.hu side.';
    }
}
