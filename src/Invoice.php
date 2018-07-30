<?php


namespace SzuniSoft\SzamlazzHu;

use SzuniSoft\SzamlazzHu\Client\Errors\InvoiceValidationException;
use SzuniSoft\SzamlazzHu\Client\Errors\ModelValidationException;
use SzuniSoft\SzamlazzHu\Client\Models\InvoiceCancellationResponse;
use SzuniSoft\SzamlazzHu\Client\Models\InvoiceCreationResponse;
use SzuniSoft\SzamlazzHu\Internal\AbstractInvoice;
use SzuniSoft\SzamlazzHu\Internal\Support\ClientAccessor;

/**
 * Class Invoice
 * @package SzuniSoft\SzamlazzHu
 */
class Invoice extends AbstractInvoice {

    use ClientAccessor;

    /**
     * The related pro forma invoice number
     *
     * @var string
     */
    protected $proFormaInvoiceNumber = null;

    /**
     * Related pro forma invoice instance
     *
     * @var ProformaInvoice
     */
    protected $proFormaInvoice = null;

    /**
     * @var null
     */
    protected $cancellationInvoiceNumber = null;

    /**
     * @var Invoice|null
     */
    protected $cancellationInvoice = null;

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
        'isPrepaymentRequest' => false,
        'isPaid' => false,
        'fulfillmentAt' => null,
    ];

    /**
     * Invoice constructor.
     * @param array $attributes
     * @param array $items
     * @param null $customer
     * @param null $merchant
     * @param null $proFormaInvoiceNumber
     */
    public function __construct(array $attributes = [], $items = [], $customer = null, $merchant = null, $proFormaInvoiceNumber = null)
    {
        parent::__construct($attributes, $items, $customer, $merchant);
        $this->proFormaInvoiceNumber = $proFormaInvoiceNumber;
    }


    /**
     * @return null|ProformaInvoice
     * @throws Client\ApiErrors\CommonResponseException
     */
    public function getProFormaInvoice()
    {
        if (!$this->proFormaInvoice) {
            if (!$this->proFormaInvoiceNumber) {
                return null;
            }
            return $this->proFormaInvoice = $this->getClient()->getProFormaInvoice($this->proFormaInvoiceNumber);
        }
        return $this->proFormaInvoice;
    }

    /**
     * @return null|AbstractInvoice|Invoice|ProformaInvoice
     * @throws Client\ApiErrors\CommonResponseException
     */
    public function getCancellationInvoice()
    {
        if (!$this->cancellationInvoiceNumber) {
            return null;
        }
        return $this->cancellationInvoice ?: ($this->cancellationInvoice = $this->getClient()->getInvoice($this->cancellationInvoiceNumber));
    }

    /**
     * Updates the invoice instance via API
     *
     * @return Invoice
     * @throws Client\ApiErrors\CommonResponseException
     */
    public function update()
    {

        $alias = $this->getClient()->getInvoice($this);

        if ($alias) {
            $this->fill($alias->attributes);
            $this->items = $alias->items;
        }

        return $this;
    }

    /**
     * @return Invoice
     * @throws InvoiceValidationException|ModelValidationException
     */
    public function validateForSave()
    {
        $this->getClient()->validateInvoiceForSaving($this);
        return $this;
    }

    /**
     * Saves local invoice to API
     *
     * @param bool $withoutPdf
     * @param null $emailSubject
     * @param null $emailMessage
     * @param InvoiceCreationResponse $response
     * @return Invoice
     * @throws InvoiceValidationException
     */
    public function save($withoutPdf = false, $emailSubject = null, $emailMessage = null, InvoiceCreationResponse &$response = null)
    {
        $response = $this->getClient()->uploadInvoice($this, $withoutPdf, $emailSubject, $emailMessage);
        return $this;
    }

    /**
     * Fully cancels invoice in API
     *
     * @param bool $withoutPdf
     * @param null $emailSubject
     * @param null $emailMessage
     * @param InvoiceCancellationResponse|null $response
     * @return Invoice
     * @throws Client\Errors\ReceiptValidationException
     * @throws InvoiceValidationException
     */
    public function cancel($withoutPdf = false, $emailSubject = null, $emailMessage = null, InvoiceCancellationResponse &$response = null)
    {
        $response = $this->getClient()->cancelInvoice($this, $withoutPdf, $emailSubject, $emailMessage);
        $this->cancellationInvoiceNumber = $response->cancellationInvoiceNumber;
        return $this;
    }

}