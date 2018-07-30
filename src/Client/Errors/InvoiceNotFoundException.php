<?php


namespace SzuniSoft\SzamlazzHu\Client\Errors;


use Exception;
use SzuniSoft\SzamlazzHu\Internal\AbstractInvoice;

class InvoiceNotFoundException extends Exception
{

    /**
     * @var string
     */
    protected $invoiceNumber;

    /**
     * InvoiceNotFoundException constructor.
     * @param string|AbstractInvoice $invoice
     * @param int $code
     */
    public function __construct($invoice, int $code = 404)
    {

        $this->invoiceNumber = $invoice instanceof AbstractInvoice ? $invoice->invoiceNumber : $invoice;
        parent::__construct("The desired invoice [$this->invoiceNumber] not found!", $code, null);
    }


    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

}