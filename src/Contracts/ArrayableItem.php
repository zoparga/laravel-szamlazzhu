<?php


namespace SzuniSoft\SzamlazzHu\Contracts;


interface ArrayableItem
{

    /**
     * [
     *  // Required values
     *  'name' => '', // Name of the item | Required
     *  'quantity' => 1, // Quantity of this ordered item | Required
     *  'quantityUnit' => 'piece', // Unit type of quantity | Required
     *  'netUnitPrice' => 100, // The unit net price of item | Required
     *  'taxRate' => 27, // The percentage of tax rate | Required
     *
     *  // Optional values
     *  'id' => '', // This is your custom product / service identifier.
     *  'taxValue' => 27, // The (total) vat value in currency | Optional - automatically calculated
     *  'totalGrossPrice' => 127, // The total gross price of items | Optional - automatically calculated
     *  'totalNetPrice' => 100, // The total net price of item | Optional - automatically calculated
     *
     *   // Comment won't be used for receipts!
     *  'comment' => '', // A single note on item | Optional
     * ]
     *
     * @return array
     */
    function toItemArray();

}