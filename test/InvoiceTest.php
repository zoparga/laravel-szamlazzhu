<?php


namespace SzuniSoft\SzamlazzHu\Tests;


use SzuniSoft\SzamlazzHu\Internal\Support\ItemHolder;
use SzuniSoft\SzamlazzHu\Invoice;

class InvoiceTest extends \Orchestra\Testbench\TestCase {

    /** @test */
    public function it_is_item_holder()
    {
        $this->assertArrayHasKey(ItemHolder::class, class_uses_recursive(Invoice::class));
    }

}