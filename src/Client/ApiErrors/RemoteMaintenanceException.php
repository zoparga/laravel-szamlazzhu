<?php


namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


/**
 * Class RemoteMaintenanceException
 * @package SzuniSoft\SzamlazzHu\Client\ApiErrors
 *
 * The remote server and services are under maintenance
 */
class RemoteMaintenanceException extends ClientException
{

    protected $code = 1;

    protected $message = 'System Maintenance, please try again in a few minutes';

    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'Internal error in Invoice Agent. Webshop does not have to do anything, maintenance team of Számlázz.hu is notified and take action.';
    }
}