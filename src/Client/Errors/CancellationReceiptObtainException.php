<?php


namespace SzuniSoft\SzamlazzHu\Client\Errors;


use Exception;
use Throwable;

class CancellationReceiptObtainException extends Exception {

    public function __construct()
    {
        parent::__construct("Cancellation receipt can be only obtained during cancellation process. The cancellation receipt number should be saved.", null, null);
    }


}