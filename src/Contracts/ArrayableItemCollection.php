<?php


namespace zoparga\SzamlazzHu\Contracts;

/**
 * Interface ArrayableInvoiceItemCollection
 * @package zoparga\SzamlazzHu\Contracts
 */
interface ArrayableItemCollection
{

    /**
     * @see ArrayableItem
     * @return ArrayableItem[]
     */
    function toItemCollectionArray();

}
