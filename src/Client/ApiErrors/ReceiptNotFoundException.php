<?php

namespace SzuniSoft\SzamlazzHu\Client\ApiErrors;


use Exception;
use SzuniSoft\SzamlazzHu\Receipt;
use Throwable;

class ReceiptNotFoundException extends Exception {

    /**
     * @var string
     */
    protected $receiptNumber;

    /**
     * ReceiptNotFoundException constructor.
     * @param Receipt|string $receipt
     * @param int $code
     */
    public function __construct($receipt, int $code = 404)
    {
        $this->receiptNumber = $receipt instanceof Receipt ? $receipt->receiptNumber : $receipt;
        parent::__construct("The desired receipt [$this->receiptNumber] not found!", $code, null);
    }


    /**
     * More detailed info of exception.
     *
     * @return string
     */
    function getInfo()
    {
        return 'Receipt number does not exist (receipt query, send or delete).';
    }
}