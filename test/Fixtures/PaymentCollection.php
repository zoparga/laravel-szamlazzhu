<?php


namespace zoparga\SzamlazzHu\Tests\Fixtures;


use zoparga\SzamlazzHu\Contracts\ArrayablePayment;
use zoparga\SzamlazzHu\Contracts\ArrayablePaymentCollection;

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
