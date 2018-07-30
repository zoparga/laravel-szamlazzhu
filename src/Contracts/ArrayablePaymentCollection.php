<?php


namespace SzuniSoft\SzamlazzHu\Contracts;


interface ArrayablePaymentCollection
{

    /**
     * @see ArrayablePayment
     * @return ArrayablePayment[]
     */
    function toPaymentCollectionArray();

}