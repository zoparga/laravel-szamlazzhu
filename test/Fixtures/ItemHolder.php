<?php


namespace zoparga\SzamlazzHu\Tests\Fixtures;


use Illuminate\Support\Collection;

class ItemHolder {
    use \zoparga\SzamlazzHu\Internal\Support\ItemHolder;

    public function __construct()
    {
        $this->items = $this->items ?: Collection::make();
    }


}
