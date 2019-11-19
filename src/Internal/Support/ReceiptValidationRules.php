<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;


use Illuminate\Validation\Rule;

trait ReceiptValidationRules {

    /**
     * @return array
     */
    public function validationRulesForObtainingReceipt()
    {
        return [
            'receiptNumber' => ['required', 'string']
        ];
    }

    /**
     * @return array
     */
    public function validationRulesForCancellingReceipt()
    {
        return [
            'receiptNumber' => ['required', 'string']
        ];
    }

    /**
     * @return array
     */
    public function validationRulesForSavingReceipt()
    {
        return [

            // Comment must be present even if it is empty
            'comment' => ['string'],
            // Payment method | only allowed
            'paymentMethod' => ['required', Rule::in($this->paymentMethods())],
            // Currency used in invoice. Make sure all related prices, costs appear in the specified currency
            'currency' => ['required'],
            // Receipt can be prefixed
            'prefix' => ['required', 'alpha_num'],
            // It's good to have an order number
            'orderNumber' => ['required', 'alpha_num'],

            /*
             * Exchange rate bank required if the currency differs from HUF
             * */
            'exchangeRateBank' => ['required_unless:currency,Ft,currency,HUF'],

            /*
             * Exchange rate is required if the currency differs from HUF
             * */
            'exchangeRate' => ['required_unless:exchangeRateBank,MNB,currency,Ft,currency,HUF',],

            /* ----------------------------------------------------------
             * Item validation
             * -------------------------------------------------------- */
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.quantityUnit' => ['required', 'string'],
            'items.*.netUnitPrice' => ['required', 'numeric'],
            'items.*.taxRate' => ['required'],
            'items.*.id' => ['sometimes'],
            'items.*.taxValue' => ['numeric'],
            'items.*.totalGrossPrice' => ['numeric'],
            'items.*.totalNetPrice' => ['numeric'],

            /* ----------------------------------------------------------
             * Payment validation
             * -------------------------------------------------------- */
            'payments' => ['array', 'min:0'],
            'payments.*.paymentMethod' => ['required', Rule::in($this->paymentMethods())],
            'payments.*.amount' => ['required', 'numeric'],
            'payments.*.comment' => ['string', 'max:75']
        ];
    }

}
