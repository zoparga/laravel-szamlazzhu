<?php


namespace SzuniSoft\SzamlazzHu\Contracts;


interface ArrayableMerchant
{

    /**
     * [
     *  // Required values
     *  'bank' => '', // Issuer bank of merchant | Required
     *  'bankAccountNumber' => '', // Bank account number of merchant | Required
     *
     *  // Optional values
     *  'replyEmailAddress' => '', // Contact, reply address of merchant | Optional
     *  'signature' => '', // Name of person who `signed` the invoice | Optional
     * ]
     *
     * @return array
     */
    function toMerchantArray();

}