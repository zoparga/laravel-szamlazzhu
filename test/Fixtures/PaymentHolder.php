<?php


namespace zoparga\SzamlazzHu\Tests\Fixtures;


use Illuminate\Support\Collection;

class PaymentHolder {

    use \zoparga\SzamlazzHu\Internal\Support\PaymentHolder;

    public function __construct()
    {
        $this->payments = Collection::make();
    }


}
