<?php


namespace SzuniSoft\SzamlazzHu\Client\Errors;


use Exception;
use SzuniSoft\SzamlazzHu\Receipt;

class ReceiptAlreadyCancelledException extends Exception {

    /**
     * @var Receipt
     */
    protected $receipt;

    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
        parent::__construct("The receipt [$receipt->receiptNumber] is already cancelled!", null, null);
    }

    /**
     * @return Receipt
     */
    public function getReceipt()
    {
        return $this->receipt;
    }


}