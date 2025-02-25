<?php


namespace zoparga\SzamlazzHu\Tests;


use zoparga\SzamlazzHu\Internal\Support\ItemHolder;
use zoparga\SzamlazzHu\Invoice;

class InvoiceTest extends \Orchestra\Testbench\TestCase {


    public function test_it_is_item_holder()
    {
        $this->assertArrayHasKey(ItemHolder::class, class_uses_recursive(Invoice::class));
    }

}
