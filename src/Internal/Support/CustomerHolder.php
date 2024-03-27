<?php

namespace zoparga\SzamlazzHu\Internal\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;
use zoparga\SzamlazzHu\Contracts\ArrayableCustomer;

/**
 * Trait SimplifiesCustomer
 * @package zoparga\SzamlazzHu\Support
 *
 * [Attributes]
 * @property-read string $customerEmail
 * @property-read string $customerName
 * @property-read string $customerCountry
 * @property-read string $customerZipCode
 * @property-read string $customerCity
 * @property-read string $customerAddress
 * @property-read string $customerTaxNumber
 * @property-read string $customerEuTaxNumber
 * @property-read int $customerTaxSubject
 * @property-read string $customerShippingName
 * @property-read string $customerShippingCountry
 * @property-read string $customerShippingZipCode
 * @property-read string $customerShippingCity
 * @property-read string $customerShippingAddress
 * @property-read boolean $customerReceivesEmail
 */
trait CustomerHolder
{
    /**
     * @var array|null
     */
    protected $customer = null;

    /**
     * @var array
     */
    protected $customerAttributes = [
        'customerEmail',
        'customerName',
        'customerCountry',
        'customerZipCode',
        'customerCity',
        'customerAddress',
        'customerTaxNumber',
        'customerEuTaxNumber',
        'customerTaxSubject',
        'customerShippingName',
        'customerShippingCountry',
        'customerShippingZipCode',
        'customerShippingCity',
        'customerShippingAddress',
        'customerReceivesEmail',
    ];

    /**
     * @param array|ArrayableCustomer
     * @return array
     */
    protected function simplifyCustomer($customer)
    {
        if (! is_array($customer) && ! $customer instanceof ArrayableCustomer) {
            throw new InvalidArgumentException("Specified customer must be an array or must implement [" . class_basename(ArrayableCustomer::class) . "]");
        }
        return ($customer instanceof ArrayableCustomer) ? $customer->toCustomerArray() : (array) $customer;
    }

    /**
     * Sets customer details on invoice
     *
     * @param array|ArrayableCustomer $customer
     */
    public function setCustomer($customer)
    {
        $customer = $this->simplifyCustomer($customer);
        foreach ($customer as $key => $value) {
            $key = ! Str::startsWith($key, 'customer') ? 'customer' . ucfirst(Str::camel($key)) : lcfirst($key);
            $this->attributes[$key] = $value;
        }
        $this->customer = $customer;
    }

    /**
     * @return array|null
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return bool
     */
    public function hasCustomer()
    {
        return $this->customer !== null;
    }

}
