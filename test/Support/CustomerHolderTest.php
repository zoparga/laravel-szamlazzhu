<?php


namespace zoparga\SzamlazzHu\Tests\Support;


use Orchestra\Testbench\TestCase;
use function PHPSTORM_META\type;
use zoparga\SzamlazzHu\Tests\Fixtures\Customer;
use zoparga\SzamlazzHu\Tests\Fixtures\CustomerHolder;

class CustomerHolderTest extends TestCase {

    /**
     * @var CustomerHolder
     */
    protected $holder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->holder = new CustomerHolder();
    }


    public function test_it_can_determine_it_has_customer()
    {
        $customer = new Customer(
            'Jane Doe',
            '9999',
            'Testable Area',
            'Secret streets',
            '9999'
        );

        $this->assertFalse($this->holder->hasCustomer());
        $this->holder->setCustomer($customer);
        $this->assertIsArray($this->holder->getCustomer());
    }


    public function test_it_can_simplify_customer_by_contract()
    {
        $customer = new Customer(
            'Jane Doe',
            '9999',
            'Testable Area',
            'Secret streets',
            '9999'
        );

        $this->holder->setCustomer($customer);
        $this->assertSame([
            'name' => 'Jane Doe',
            'zipCode' => '9999',
            'city' => 'Testable Area',
            'address' => 'Secret streets',
            'taxNumber' => '9999',
            'receivesEmail' => false,
            'email' => null
        ], $this->holder->getCustomer());
    }


    public function test_it_can_simplify_customer_by_array()
    {

        $customer = [
            'name' => 'Jane Doe',
            'zipCode' => '9999',
            'city' => 'Testable Area',
            'address' => 'Secret streets',
            'taxNumber' => '9999'
        ];

        $this->holder->setCustomer($customer);
        $this->assertSame([
            'name' => 'Jane Doe',
            'zipCode' => '9999',
            'city' => 'Testable Area',
            'address' => 'Secret streets',
            'taxNumber' => '9999'
        ], $this->holder->getCustomer());
    }

}
