<?php


namespace zoparga\SzamlazzHu\Tests\Client\Invoice;


use zoparga\SzamlazzHu\Client\Client;
use zoparga\SzamlazzHu\Invoice;

class TestCase extends \zoparga\SzamlazzHu\Tests\Client\TestCase {

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
