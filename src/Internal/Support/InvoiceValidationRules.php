<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;


use Illuminate\Validation\Rule;
use SzuniSoft\SzamlazzHu\Invoice;

trait InvoiceValidationRules
{

    /**
     * @return array
     */
    public function validationRulesForDeletingProformaInvoice()
    {
        return [
            'invoiceNumber' => ['required', 'string'],
        ];
    }

    /**
     * @return array
     */
    public function validationRulesForCancellingInvoice()
    {
        return [
            // The invoice is electric
            'isElectronic'              => ['required', 'boolean'],
            'invoiceNumber'             => ['required', 'string'],

            /* ----------------------------------------------------------
             * Merchant fields
             * -------------------------------------------------------- */
            'merchantReplyEmailAddress' => ['email'],

            /* ----------------------------------------------------------
             * Customer fields
             * -------------------------------------------------------- */
            'customerEmail'             => ['email'],
        ];
    }

    /**
     * @return array
     */
    public function validationRulesForSavingInvoice()
    {
        return [
            // The invoice is electronic
            'isElectronic'    => ['required', 'boolean'],
            // Comment must be present even if it is empty
            'comment'         => ['string'],
            // The language of invoice (and email)
            'invoiceLanguage' => ['required', Rule::in(Invoice::$supportedLanguages)],
            // Currency used in invoice. Make sure all related prices, costs appear in the specified currency
            'currency'        => ['required'],
            // Datetime fields
            'createdAt'       => ['required', 'date'],
            'fulfillmentAt'   => ['required', 'date'],
            'paymentDeadline' => ['required', 'date'],
            // Payment method | only allowed
            'paymentMethod'   => ['required', Rule::in($this->paymentMethods())],

            // Invoice is prefixed
            'invoicePrefix'   => ['string'],

            // This is usually the locally stored incremental identifier of order.
            // It is important to be specified because the common invoice can be
            // obtained from proforma invoice only if it is specified.
            'orderNumber'     => ['required', 'alpha_num'],

            'isImprestInvoice'          => ['required', 'boolean'],
            'isFinalInvoice'            => ['required', 'boolean'],
            'isReplacementInvoice'      => ['required', 'boolean'],
            'isPrepaymentRequest'       => ['required', 'boolean'],

            /*
             * Exchange rate bank required if the currency differs from HUF
             * */
            'exchangeRateBank'          => ['required_unless:currency,Ft,currency,HUF'],

            /*
             * Exchange rate is required if the currency differs from HUF
             * */
            'exchangeRate' => [
                'required_unless:currency,Ft,currency,HUF',
            ],

            /* ----------------------------------------------------------
             * Merchant fields
             * -------------------------------------------------------- */
            'merchantBank'              => ['nullable'],
            'merchantBankAccountNumber' => ['nullable', 'string'],
            'merchantReplyEmailAddress' => ['nullable', 'email'],

            /* ----------------------------------------------------------
             * Customer fields
             * -------------------------------------------------------- */
            'customerName'              => ['required', 'string', 'max:255'],
            'customerZipCode'           => ['required', 'string', 'max:255'],
            'customerCity'              => ['required', 'string', 'max:255'],
            'customerAddress'           => ['required', 'string', 'max:255'],
            'customerEmail'             => ['email'],
            'customerReceivesEmail'     => ['boolean'],
            'customerTaxNumber'         => ['string', 'alpha_dash', 'nullable'],
            'customerShippingName'      => ['string', 'max:255'],
            'customerShippingZipCode'   => ['string', 'max:255'],
            'customerShippingCity'      => ['string', 'max:255'],
            'customerShippingAddress'   => ['string', 'max:255'],

            /* ----------------------------------------------------------
             * Items contained by the invoice
             * -------------------------------------------------------- */
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.name'              => ['required', 'string'],
            'items.*.quantity'          => ['required', 'integer', 'min:1'],
            'items.*.quantityUnit'      => ['required', 'string'],
            'items.*.netUnitPrice'      => ['required', 'numeric'],
            'items.*.taxRate'           => ['required'],
            'items.*.id'                => ['sometimes'],
            'items.*.taxValue'          => ['numeric'],
            'items.*.totalGrossPrice'   => ['numeric'],
            'items.*.totalNetPrice'     => ['numeric'],
            'items.*.comment'           => ['sometimes', 'string'],
        ];
    }

}
