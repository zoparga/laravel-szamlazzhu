<?php


namespace SzuniSoft\SzamlazzHu\Tests\Fixtures;


use SzuniSoft\SzamlazzHu\Contracts\ArrayableCustomer;

class Customer implements ArrayableCustomer {
    protected $name;
    protected $zipCode;
    protected $city;
    protected $address;
    protected $taxNumber;
    /**
     * @var bool
     */
    protected $receivesEmail;
    /**
     * @var null
     */
    protected $email;

    /**
     * Customer constructor.
     * @param $name
     * @param $zipCode
     * @param $city
     * @param $address
     * @param $taxNumber
     * @param bool $receivesEmail
     * @param null $email
     */
    public function __construct($name, $zipCode, $city, $address, $taxNumber, $receivesEmail = false, $email = null)
    {
        $this->name = $name;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->address = $address;
        $this->taxNumber = $taxNumber;
        $this->receivesEmail = $receivesEmail;
        $this->email = $email;
    }


    /**
     * [
     *  // Required values
     *  'name' => '', // Name of the customer | Required
     *  'zipCode' => '', // Zip postal code of customer | Required
     *  'city' => '', // City of customer | Required
     *  'address' => '', // Address of customer | Required
     *  'taxNumber' => '', // Tax number of customer | Required
     *
     *  // Optional values
     *  'receivesEmail' => '', // The customer will receive invoice email | Optional
     *  'email' => '', // Email address of customer | Optional
     * ]
     *
     * @return array
     */
    function toCustomerArray()
    {
        return [
            'name' => $this->name, // Name of the customer | Required
            'zipCode' => $this->zipCode, // Zip postal code of customer | Required
            'city' => $this->city, // City of customer | Required
            'address' => $this->address, // Address of customer | Required
            'taxNumber' => $this->taxNumber, // Tax number of customer | Required

            // Optional values
            'receivesEmail' => $this->receivesEmail, // The customer will receive invoice email | Optional
            'email' => $this->email, // Email address of customer | Optional
        ];
    }
}