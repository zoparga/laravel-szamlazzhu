<?php


namespace SzuniSoft\SzamlazzHu\Tests\Fixtures;


use SzuniSoft\SzamlazzHu\Contracts\ArrayableItem;

class Item implements ArrayableItem {
    public $name;
    public $quantity;
    public $quantityUnit;
    public $netUnitPrice;
    public $taxRate;
    /**
     * @var null
     */
    public $id;
    /**
     * @var null
     */
    public $taxValue;
    /**
     * @var null
     */
    public $totalGrossPrice;
    /**
     * @var null
     */
    public $totalNetPrice;
    /**
     * @var string
     */
    public $comment;

    /**
     * Item constructor.
     * @param $name
     * @param $quantity
     * @param $quantityUnit
     * @param $netUnitPrice
     * @param $taxRate
     * @param null $id
     * @param null $taxValue
     * @param null $totalGrossPrice
     * @param null $totalNetPrice
     * @param string $comment
     */
    public function __construct(
        $name,
        $quantity,
        $quantityUnit,
        $netUnitPrice,
        $taxRate,
        $id = null,
        $taxValue = null,
        $totalGrossPrice = null,
        $totalNetPrice = null,
        $comment = ''
    )
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->quantityUnit = $quantityUnit;
        $this->netUnitPrice = $netUnitPrice;
        $this->taxRate = $taxRate;
        $this->id = $id;
        $this->taxValue = $taxValue;
        $this->totalGrossPrice = $totalGrossPrice;
        $this->totalNetPrice = $totalNetPrice;
        $this->comment = $comment;
    }


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
    function toItemArray()
    {
        return [
            'name' => $this->name, // Name of the item | Required
            'quantity' => $this->quantity, // Quantity of this ordered item | Required
            'quantityUnit' => $this->quantityUnit, // Unit type of quantity | Required
            'netUnitPrice' => $this->netUnitPrice, // The unit net price of item | Required
            'taxRate' => $this->taxRate, // The percentage of tax rate | Required

            // Optional values
            'id' => $this->id, // This is your custom product / service identifier.
            'taxValue' => $this->taxValue, // The (total) vat value in currency | Optional - automatically calculated
            'totalGrossPrice' => $this->totalGrossPrice, // The total gross price of items | Optional - automatically calculated
            'totalNetPrice' => $this->totalNetPrice, // The total net price of item | Optional - automatically calculated

            // Comment won't be used for receipts!
            'comment' => $this->comment, // A single note on item | Optional
        ];
    }
}