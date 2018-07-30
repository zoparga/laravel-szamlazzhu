<?php


namespace SzuniSoft\SzamlazzHu\Tests\Fixtures;


use Illuminate\Support\Collection;

class ItemHolder {
    use \SzuniSoft\SzamlazzHu\Internal\Support\ItemHolder;

    public function __construct()
    {
        $this->items = $this->items ?: Collection::make();
    }


}