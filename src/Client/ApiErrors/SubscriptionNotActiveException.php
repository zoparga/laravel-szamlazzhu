<?php


namespace zoparga\SzamlazzHu\Client\ApiErrors;


/**
 * Class SubscriptionNotActiveException
 * @package zoparga\SzamlazzHu\Client\ApiErrors
 *
 * The Invoice Agent cannot be used because of an account/subscription issue
 */
class SubscriptionNotActiveException extends ClientException
{

    protected $code = 136;

    protected $message = 'Login error. Please log in to Szamlazz.hu through your browser.';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'The Invoice Agent cannot be used for some reason. Possible causes: the subscription has expired, there is a pending invoice towards Szamlazz.hu, or there is a delay in payment. Please log in to Szamlazz.hu and check the subscription under "Szolgáltatáscsomagom".';
    }
}
