<?php


namespace SzuniSoft\SzamlazzHu;

use SzuniSoft\SzamlazzHu\Client\ApiErrors\CommonResponseException;
use SzuniSoft\SzamlazzHu\Client\Errors\InvoiceValidationException;
use SzuniSoft\SzamlazzHu\Client\Errors\ModelValidationException;
use SzuniSoft\SzamlazzHu\Client\Errors\UnknownOrderIdException;
use SzuniSoft\SzamlazzHu\Client\Models\InvoiceCreationResponse;
use SzuniSoft\SzamlazzHu\Client\Models\ProformaInvoiceDeletionResponse;
use SzuniSoft\SzamlazzHu\Internal\AbstractInvoice;
use SzuniSoft\SzamlazzHu\Internal\Support\ClientAccessor;

/**
 * Class ProFormaInvoice
 * @package SzuniSoft\SzamlazzHu
 *
 * Pro-forma invoice is a kind of special invoice which is used to
 * send an `invoice` that is a prepayment request to the customer.
 */
class ProformaInvoice extends AbstractInvoice {

    use ClientAccessor;

    /**
     * @var Invoice
     */
    protected $orderInvoice = null;

    /**
     * Default attributes
     * Important!
     * All the pre-defined attributes are going to be created as
     * XML tags even with an empty value. Do not add further ones.
     *
     * @var array
     */
    protected $attributes = [
        'isElectronic' => true,
        'comment' => '',
        'invoiceLanguage' => 'en',
        'currency' => 'EUR',
        'createdAt' => null,
        'paymentDeadline' => null,
        'isImprestInvoice' => false,
        'isFinalInvoice' => false,
        'isReplacementInvoice' => false,
        'isPrepaymentRequest' => true,
        'isPaid' => false,
        'fulfillmentAt' => null,
    ];

    public function __construct($attributes = null, $items = null, $customer = null, $merchant = null)
    {
        parent::__construct($attributes, $items, $customer, $merchant);
    }

    /**
     * @return AbstractInvoice|Invoice|ProformaInvoice
     * @throws CommonResponseException
     * @throws UnknownOrderIdException
     */
    public function orderInvoice()
    {
        if (!$this->orderInvoice && !$this->orderNumber) {
            throw new UnknownOrderIdException("No order id is provided on Proforma invoice! Order invoice could not be obtained.");
        }

        if (!$this->orderInvoice) {

            $invoice = $this->getClient()->getInvoiceByOrderNumber($this->orderNumber);
            if ($invoice && $invoice instanceof Invoice) {
                $this->orderInvoice = $invoice;
            }

        }

        return $this->orderInvoice;
    }

    /**
     * @return ProformaInvoice
     * @throws CommonResponseException
     */
    public function update()
    {
        $alias = $this->getClient()->getProformaInvoice($this);

        if ($alias) {
            $this->fill($alias->attributes);
            $this->items = $alias->items;
        }

        return $this;
    }

    /**
     * @return $this
     * @throws InvoiceValidationException|ModelValidationException
     */
    public function validateForSave()
    {
        $this->getClient()->validateProformaInvoiceForSaving($this);
        return $this;
    }

    /**
     * @param bool $withoutPdf
     * @param null $emailSubject
     * @param null $emailMessage
     * @param InvoiceCreationResponse $response
     * @return ProformaInvoice
     * @throws ModelValidationException
     */
    public function save($withoutPdf = false, $emailSubject = null, $emailMessage = null, InvoiceCreationResponse &$response = null)
    {
        $response = $this->getClient()->uploadProFormaInvoice($this, $withoutPdf, $emailSubject, $emailMessage);
        return $this;
    }

    /**
     * @param ProformaInvoiceDeletionResponse $response
     * @return ProformaInvoice
     * @throws ModelValidationException
     */
    public function delete(ProformaInvoiceDeletionResponse &$response = null)
    {
        $response = $this->getClient()->deleteProFormaInvoice($this);
        return $this;
    }

}