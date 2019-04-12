<?php


namespace SzuniSoft\SzamlazzHu\Tests\Support;


use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\Payment;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\PaymentCollection;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\PaymentHolder;

class PaymentHolderTest extends TestCase {

    /**
     * @var PaymentHolder
     */
    protected $holder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->holder = new PaymentHolder();
    }

    /**
     * @return array
     */
    protected function paymentArray()
    {
        return [
            'paymentMethod' => 'c.o.d.',
            'amount' => 5000,
            'comment' => null
        ];
    }

    /**
     * @return Payment
     */
    protected function payment()
    {
        return new Payment(...array_values($this->paymentArray()));
    }

    /**
     * @param int $times
     * @return array
     */
    protected function payments($times = 1)
    {
        return Collection::times($times, function () {
            return $this->payment();
        })->toArray();
    }

    /** @test */
    public function it_can_simplify_payment()
    {
        $this->holder->addPayment($this->payment());
        $this->assertSame($this->paymentArray(), $this->holder->payments()->first());
    }

    /** @test */
    public function it_can_accept_associative_payment()
    {
        $payment = $this->paymentArray();
        $this->holder->addPayment($payment);
        $this->assertSame($payment, $this->holder->payments()->first());
    }

    /** @test */
    public function it_can_add_multiple_payments()
    {
        $this->holder->addPayments($this->payments(5));
        $this->assertEquals(5, $this->holder->payments()->count());
    }

    /** @test */
    public function it_can_add_items_via_collection()
    {
        $this->holder->addPayments(new PaymentCollection($this->payments(5)));
        $this->assertEquals(5, $this->holder->payments()->count());
    }

    /** @test */
    public function it_can_tell_if_it_is_empty()
    {
        $this->assertTrue($this->holder->isEmpty());
        $this->assertFalse($this->holder->hasPayment());
    }

    /** @test */
    public function it_can_provide_numeric_array_representation()
    {

        $payments = $this->payments();

        $itemsArray = array_map(function (Payment $value) {
            return $value->toPaymentArray();
        }, $payments);

        $this->holder->addPayments($payments);
        $this->assertSame(
            $itemsArray,
            $this->holder->paymentsToArray()
        );
    }

    /** @test */
    public function it_has_total_count_shortcut()
    {
        $this->holder->addPayments($this->payments(10));
        $this->assertEquals(10, $this->holder->payments()->count());
        $this->assertEquals(10, $this->holder->total());
    }

}
