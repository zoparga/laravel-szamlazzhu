<?php


namespace SzuniSoft\SzamlazzHu\Tests\Fixtures;


use SzuniSoft\SzamlazzHu\Contracts\ArrayablePayment;
use SzuniSoft\SzamlazzHu\Contracts\ArrayablePaymentCollection;

class PaymentCollection implements ArrayablePaymentCollection {

    /**
     * @var array
     */
    protected $payments;

    /**
     * PaymentCollection constructor.
     * @param array $payments
     */
    public function __construct(array $payments)
    {
        $this->payments = $payments;
    }


    /**
     * @see ArrayablePayment
     * @return ArrayablePayment[]
     */
    function toPaymentCollectionArray()
    {
        return $this->payments;
    }
}