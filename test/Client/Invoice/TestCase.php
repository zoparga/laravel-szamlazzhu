<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client\Invoice;


use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\Invoice;

class TestCase extends \SzuniSoft\SzamlazzHu\Tests\Client\TestCase {

    /**
     * @param null $number
     * @param Client|null $client
     * @return Invoice
     */
    protected function getEmptyInvoice($number = null, Client $client = null)
    {
        $invoice = new Invoice();

        if ($client) {
            $invoice->setClient($client);
        }

        if ($number) {
            $invoice->invoiceNumber = $number;
        }

        return $invoice;
    }

}