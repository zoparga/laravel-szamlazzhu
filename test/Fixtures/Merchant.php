<?php


namespace SzuniSoft\SzamlazzHu\Tests\Fixtures;


use SzuniSoft\SzamlazzHu\Contracts\ArrayableMerchant;

class Merchant implements ArrayableMerchant {
    protected $bank;
    protected $bankAccountNumber;
    /**
     * @var null
     */
    protected $replyEmailAddress;
    /**
     * @var null
     */
    protected $signature;

    /**
     * Merchant constructor.
     * @param $bank
     * @param $bankAccountNumber
     * @param null $replyEmailAddress
     * @param null $signature
     */
    public function __construct(
        $bank,
        $bankAccountNumber,
        $replyEmailAddress = null,
        $signature = null
    )
    {
        $this->bank = $bank;
        $this->bankAccountNumber = $bankAccountNumber;
        $this->replyEmailAddress = $replyEmailAddress;
        $this->signature = $signature;
    }


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
    function toMerchantArray()
    {
        return [
            // Required values
            'bank' => $this->bank, // Issuer bank of merchant | Required
            'bankAccountNumber' => $this->bankAccountNumber, // Bank account number of merchant | Required

            // Optional values
            'replyEmailAddress' => $this->replyEmailAddress, // Contact, reply address of merchant | Optional
            'signature' => $this->signature, // Name of person who `signed` the invoice | Optional
        ];
    }
}