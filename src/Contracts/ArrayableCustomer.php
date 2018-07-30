<?php


namespace SzuniSoft\SzamlazzHu\Contracts;


interface ArrayableCustomer
{

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
    function toCustomerArray();

}