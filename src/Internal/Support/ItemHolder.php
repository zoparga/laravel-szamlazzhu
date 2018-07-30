<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItem;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItemCollection;

/**
 * Trait SimplifiesItem
 * @package SzuniSoft\SzamlazzHu\Support
 */
trait ItemHolder {

    /**
     * @var Collection
     */
    protected $items;

    /**
     * @param array|ArrayableItem $item
     * @return array
     */
    protected function simplifyItem($item)
    {
        if (!is_array($item) && !$item instanceof ArrayableItem) {
            throw new InvalidArgumentException("Specified item must be an array or must implement [" . class_basename(ArrayableItem::class) . "]");
        }
        return ($item instanceof ArrayableItem) ? $item->toItemArray() : $item;
    }

    /**
     * Adds one or multiple items to invoice.
     *
     * @see ArrayableItem
     * @param array|ArrayableItem|ArrayableItemCollection $item
     */
    public function addItem($item)
    {

        if ($item instanceof ArrayableItemCollection) {
            Collection::wrap($item->toItemCollectionArray())->each(function ($item) {
                $this->addItem($item);
            });
        }

        $this->items->push($this->simplifyItem($item));
    }

    /**
     * @param array|ArrayableItemCollection $items
     */
    public function addItems($items)
    {
        if ($items instanceof ArrayableItemCollection) {
            $items = $items->toItemCollectionArray();
        }

        if (count($items) > 0 && is_numeric(array_keys($items)[0])) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
        }
        else {
            $this->addItem($items);
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->items->isEmpty();
    }

    /**
     * @return bool
     */
    public function hasItem()
    {
        return !$this->isEmpty();
    }

    /**
     * @return Collection
     */
    public function items()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->items->count();
    }

    /**
     * @return array
     */
    public function itemsToArray()
    {
        return $this->items->toArray();
    }

    /**
     * Removes an item from the list by identifier.
     * This method removes all the items that were found with the given id.
     * Please note that if no id is provided on the item it won't have any effect at all!
     *
     * @param $id
     */
    public function removeItem($id)
    {

        $this->items = $this->items->reject(function ($item) use (&$id) {
            if (isset($item['id']) && $item['id'] === $id) {
                return true;
            }
            return false;
        });

    }

}