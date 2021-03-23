<?php


namespace zoparga\SzamlazzHu\Contracts;


interface ArrayablePaymentCollection
{

    /**
     * @see ArrayablePayment
     * @return ArrayablePayment[]
     */
    function toPaymentCollectionArray();

}
