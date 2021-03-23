<?php


namespace zoparga\SzamlazzHu\Tests\Fixtures;


use zoparga\SzamlazzHu\Contracts\ArrayablePayment;

class Payment implements ArrayablePayment {
    protected $paymentMethod;
    protected $amount;
    protected $comment;

    /**
     * Payment constructor.
     * @param $paymentMethod
     * @param $amount
     * @param null $comment
     */
    public function __construct(
        $paymentMethod,
        $amount,
        $comment = null
    )
    {
        $this->paymentMethod = $paymentMethod;
        $this->amount = $amount;
        $this->comment = $comment;
    }


    /**
     * [
     *  'paymentMethod' => '', // @see \zoparga\SzamlazzHu\Internal\Support\PaymentMethods::$paymentMethods
     *  'amount' => '', // The amount was paid
     *  'comment' => '', // A single note on payment
     * ]
     *
     * @return array
     */
    function toPaymentArray()
    {
        return [
            'paymentMethod' => $this->paymentMethod, // @see \zoparga\SzamlazzHu\Internal\Support\PaymentMethods::$paymentMethods
            'amount' => $this->amount, // The amount was paid
            'comment' => $this->comment, // A single note on payment
        ];
    }
}
