<?php


namespace SzuniSoft\SzamlazzHu\Tests\Fixtures;


use Illuminate\Support\Collection;

class PaymentHolder {

    use \SzuniSoft\SzamlazzHu\Internal\Support\PaymentHolder;

    public function __construct()
    {
        $this->payments = Collection::make();
    }


}