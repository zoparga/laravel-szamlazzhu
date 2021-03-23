<?php


namespace zoparga\SzamlazzHu\Contracts;

use zoparga\SzamlazzHu\Internal\Support\PaymentMethods;

/**
 * Interface ArrayablePayment
 * @package zoparga\SzamlazzHu\Contracts
 */
interface ArrayablePayment
{

    /**
     * [
     *  'paymentMethod' => '', // @see \zoparga\SzamlazzHu\Internal\Support\PaymentMethods::$paymentMethods
     *  'amount' => '', // The amount was paid
     *  'comment' => '', // A single note on payment
     * ]
     *
     * @return array
     */
    function toPaymentArray();

}
