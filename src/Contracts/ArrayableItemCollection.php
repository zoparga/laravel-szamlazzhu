<?php


namespace SzuniSoft\SzamlazzHu\Contracts;

/**
 * Interface ArrayableInvoiceItemCollection
 * @package SzuniSoft\SzamlazzHu\Contracts
 */
interface ArrayableItemCollection
{

    /**
     * @see ArrayableItem
     * @return ArrayableItem[]
     */
    function toItemCollectionArray();

}