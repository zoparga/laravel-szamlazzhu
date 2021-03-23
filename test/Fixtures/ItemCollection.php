<?php


namespace zoparga\SzamlazzHu\Tests\Fixtures;


use zoparga\SzamlazzHu\Contracts\ArrayableItem;
use zoparga\SzamlazzHu\Contracts\ArrayableItemCollection;

class ItemCollection implements ArrayableItemCollection {

    /**
     * @var array
     */
    protected $items;

    /**
     * ItemCollection constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }


    /**
     * @see ArrayableItem
     * @return ArrayableItem[]
     */
    function toItemCollectionArray()
    {
        return $this->items;
    }
}
