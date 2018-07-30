<?php


namespace SzuniSoft\SzamlazzHu\Contracts;

use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;

/**
 * Interface ArrayablePayment
 * @package SzuniSoft\SzamlazzHu\Contracts
 */
interface ArrayablePayment
{

    /**
     * [
     *  'paymentMethod' => '', // @see \SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods::$paymentMethods
     *  'amount' => '', // The amount was paid
     *  'comment' => '', // A single note on payment
     * ]
     *
     * @return array
     */
    function toPaymentArray();

}