<?php


namespace SzuniSoft\SzamlazzHu\Tests\Support;


use Orchestra\Testbench\TestCase;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\Merchant;
use SzuniSoft\SzamlazzHu\Tests\Fixtures\MerchantHolder;

class MerchantHolderTest extends TestCase {

    /**
     * @var MerchantHolder
     */
    protected $holder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->holder = new MerchantHolder();
    }

    /**
     * @return Merchant
     */
    protected function merchant()
    {
        return new Merchant(
            'bank',
            '123'
        );
    }

    /** @test */
    public function it_can_determine_it_has_merchant()
    {
        $this->assertFalse($this->holder->hasMerchant());
        $this->holder->setMerchant($this->merchant());
        $this->assertTrue($this->holder->hasMerchant());
    }

    /** @test */
    public function it_can_accept_associative_merchant()
    {
        $merchant = [
            'bank' => 'bank',
            'bankAccountNumber' => '123',
            'replyEmailAddress' => null,
            'signature' => null
        ];
        $this->holder->setMerchant($merchant);
        $this->assertSame($merchant, $this->holder->getMerchant());
    }

    /** @test */
    public function it_can_simplify_merchant()
    {
        $this->holder->setMerchant($this->merchant());
        $this->assertSame([
            'bank' => 'bank',
            'bankAccountNumber' => '123',
            'replyEmailAddress' => null,
            'signature' => null
        ], $this->holder->getMerchant());
    }

}
