<?php


namespace zoparga\SzamlazzHu\Tests;


use zoparga\SzamlazzHu\Internal\Support\ItemHolder;
use zoparga\SzamlazzHu\Internal\Support\PaymentHolder;
use zoparga\SzamlazzHu\Receipt;

class ReceiptTest extends \Orchestra\Testbench\TestCase {


    public function test_it_is_item_holder()
    {
        $this->assertArrayHasKey(ItemHolder::class, class_uses(Receipt::class));
    }


    public function test_it_is_payment_holder()
    {
        $this->assertArrayHasKey(PaymentHolder::class, class_uses(Receipt::class));
    }


    public function test_it_has_item_total_alias()
    {
        $this->assertTrue(method_exists(new Receipt(), 'totalItems'));
    }


    public function test_it_has_payment_total_alias()
    {
        $this->assertTrue(method_exists(new Receipt(), 'totalPayments'));
    }

}
