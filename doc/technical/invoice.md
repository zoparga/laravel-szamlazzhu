# Invoices / Proforma invoices
This documentation represents the common invoice implementation only but you can use the same methods when you create ProformaInvoice instances.

## Invoice types

Class | Namespace
--- | ---
Invoice | SzuniSoft\SzamlazzHu\Invoice
ProformaInvoice | SzuniSoft\SzamlazzHu\ProformaInvoice

## Contract types

Contract | Namespace
--- | ---
ArrayableMerchant | SzuniSoft\SzamlazzHu\Contracts\ArrayableMerchant
ArrayableCustomer | SzuniSoft\SzamlazzHu\Contracts\ArrayableCustomer
ArrayableItem | SzuniSoft\SzamlazzHu\Contracts\ArrayableItem
ArrayableItemCollection | SzuniSoft\SzamlazzHu\Contracts\ArrayableItemCollection

## Initialize invoice
```php
use SzuniSoft\SzamlazzHu\Invoice;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;

$invoice = new Invoice(); // Or: new ProformaInvoice();

/*
 * Required attributes 
 */

// Alpha numeric
$invoice->orderNumber = 123;
// Boolean
$invoice->isElectronic = true;
// Supported: de, en, it, hu, fr, ro, sk, hr
$invoice->invoiceLanguage = 'en';
$invoice->currency = 'EUR';
$invoice->fulfillmentAt = Carbon::now();
$invoice->paymentDeadline = Carbon::now()->addMonth();
// Supported: transfer, cash, bank_card, credit_card, check, c.o.d., 
// gift_card, barter, Borgun, group, EP_card, OTP_simple, compensation, coupon, 
// PayPal, PayU, SZÉP_card, free_of_charge, voucher
$invoice->paymentMethod = PaymentMethods::$paymentMethods['c.o.d.'];
// Boolean
$invoice->isImprestInvoice = false;
// Boolean
$invoice->isFinalInvoice = false;

/*
 * Required when currency is not in HUF or Ft 
 */
 $invoice->exchangeRateBank = 'Some bank';
 $invoice->exchangeRate = 55;

/*
 * Optional attributes 
 */
$invoice->invoicePrefix = 'PRFX';
$invoice->comment = 'Wow! This is an invoice!';
```
#### Note on exchange rate bank
If the currency is foreign (differs from HUF or Ft) you have to specify the currency exchange bank and rate. But if the provided exchange bank equals to "**MNB**" _(Magyar Nemzeti Bank)_ the API will find out the rate automatically. 

## Setup customer on invoice

### Add customer as array
```php
$invoice->setCustomer([
    // Required
    'name' => 'John Doe',
    'zipCode' => '1234',
    'city' => 'Foreign City',
    'address' => 'Some street 99',
    'taxNumber' => '1234',
    // Optional
    'receivesEmail' => true, // Receives email notifications.
    'email' => 'john.doe@example.com'
]);
```

### Add customer as ArrayableCustomer contract
```php
use SzuniSoft\SzamlazzHu\Contracts\ArrayableCustomer;

class ThisIsMyCustomerClass implements ArrayableCustomer {

    public function toCustomerArray() {
        return [
            'name' => 'John Doe',
            'zipCode' => '1234',
            'city' => 'Foreign City',
            'address' => 'Some street 99',
            'taxNumber' => '1234',
            'receivesEmail' => true,
            'email' => 'john.doe@example.com'
        ];
    }
}
```

```php
$thisIsMyCustomer = new ThisIsMyCustomerClass();
$invoice->setCustomer($thisIsMyCustomer);
```

## Setup merchant on invoice (optional)
You only need to specify merchant details when you have not setup default merchant details in the configuration or when the merchant is dynamically assigned.

### Setup merchant as array
```php
$invoice->setMerchant([
    // Reqruired
    'bank' => 'Merchant favorite bank',
    'bankAccountNumber' => '123..',
    // Optional
    'replyEmailAddress' => 'info@merchant.mrchnt',
    'signature' => 'Mr. Boss'
]);
```

### Setup merchant as ArrayableMerchant contract
There's a contract you can use on any class: _**SzuniSoft\SzamlazzHu\Contracts\ArrayableMerchant**_

```php
use SzuniSoft\SzamlazzHu\Contracts\ArrayableMerchant;

class ThisIsMyMerchantClass implements ArrayableMerchant {

    public function toMerchantArray() {
        return [
            'bank' => 'Merchant favorite bank',
            'bankAccountNumber' => '123..',
            'replyEmailAddress' =>'info@merchant.mrchnt',
            'signature' => 'Mr. Boss'
        ];
    }

}
```
```php
$thisIsMyMerchant = new ThisIsMyMerchantClass();
$invoice->setMerchant($thisIsMyMerchant);
```

### Add items to invoice

#### Add item as array
You need to add at least one item to the invoice.

```php
$invoice->addItem([
    // Required
    'name' => 'Product',
    'quantity' => 5.0,
    'quantityUnit' => 'piece',
    'netUnitPrice' => 15.0,
    'taxRate' => 10.0,
    // Optional
    'id' => 123, 
    'taxValue' => 'automatically calculated..',
    'totalGrossPrice' => 'automatically calculated..',
    'totalNetPrice' => 'automatically calculated..',
    'comment' => 'Some note on this item'
]);
```

#### Add item as ArrayableItem contract
You may add items as classes that implements ArrayableItem contract

```php
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItem;

class ThisIsMyProductClass implements ArrayableItem {

    public function toItemArray() {
        return [
            'name' => $this->name,
            'quantity' => $order->quantity,
            'quantityUnit' => $this->qty_unit_type,
            'netUnitPrice' => $this->net_price,
            'taxRate' => 10.0,
        ];
    }

}
```
```php
$thisIsMyProduct = new ThisIsMyProductClass();
$invoice->addItem($thisIsMyProduct);
```

#### Add multiple items at once via ArrayableItemCollection
Method needs to return with items wrapped in array or **_ArrayableItem_** contracts in an array.
```php
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItemCollection;

class Cart implemenst ArrayableItemCollection {

    protected $products = [];

    public function __construct() {
        $this->products = [
            new ThisIsMyProductClass(),
            new ThisIsMyProductClass(),
            // ...
        ];
    }
    
    public function toItemCollectionArray() {
        return $this->products;
    };
}
```
```php
$cart = new Cart();
$invoice->addItems($cart);
```

### Initialize invoice with inline arguments
```php
$attributes = []; // Common invoice attributes.
$items = []; // The list of items invoice needs to contain.
$customer = []; // Customer assigned on the invoice.
$merchant = []; // Merchant assigned on the invoice.
$invoice = new Invoice($attributes, $items, $customer, $merchant);
```

## Saving the invoice
```php
$withoutPdf = false; // You can override PDF auto saving.
$invoice->save($withoutPdf);

$invoice->invoiceNumber; // After saving you can access to the invoice number.
```

## Obtaining invoices
There are several ways you can obtain an invoice.

## Query invoices
### By invoice number (getInvoice)
```php
$invoiceNumber = 'XXX-2018-123';
$invoice = $client->getInvoice($invoiceNumber); // Result is instance of Invoice or null if not found.
```

### With failure (getInvoiceOrFail)
```php
$invoiceNumber = 'XXX-2018-123';
try {
    $client->getInvoiceOrFail($invoiceNumber);
} catch(InvoiceNotFoundException $exception) {
    // Ops, invoice not found!
}
```

## Query proforma invoices
### By invoice number (getProformaInvoice)
```php
$proformaInvoiceNumber = 'D-2018-123';
$proformaInvoice = $client->getProformaInvoice($proformaInvoiceNumber); // Result is instance of ProformaInvoice or null if not found.
```

### With failure (getProformaInvoiceOrFail)
```php
$proformaInvoiceNumber = 'D-2018-123';
try {
    $client->getProformaInvoice($proformaInvoiceNumber);
} catch(InvoiceNotFoundException $exception) {
    // Ops, invoice not found!
}
```

## Query invoice by order number
```php
$orderNumber = 123;
$client->getInvoiceByOrderNumber($orderNumber); // Result is instance of ProformaInvoice or Invoice or null if not found.
```

## Query invoice with failure
```php
$orderNumber = 123;
try {
    $client->getInvoiceByOrderNumberOrFail($orderNumber);
} catch(InvoiceNotFoundException $exception) {
    // Ops, invoice not found!
}
```

## Obtained invoice
### Returned information about queried invoice
```php
$invoice = $client->getInvoice('XXX-2018-123');

// Access items
foreach ($invoice->items() as $item) {
    // We have the item here!
}

// Access merchant info
$merchant = $invoice->getMerchant();

// Access customer info
$customer = $invoice->getCustomer();
```

## Cancelling invoice (reverse)
```php
$invoice = $client->getInvoice('XXX-2018-123');
$invoice->cancel($withoutPdf = false);
$cancellationInvoice = $invoice->getCancellationInvoice();
// Work with cancellation invoice..
```

## Deleting proforma invoice
```php
$proformaInvoice = $client->getProformaInvoice('D-2018-123');
$proformaInvoice->delete();
```

## Update invoice details
You can refresh both invoice and proforma invoice attributes.
```php
$invoice->update();
$proformaInvoice->update();
```
