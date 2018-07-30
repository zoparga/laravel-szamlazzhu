# Receipts
Class accessible as **_SzuniSoft\SzamlazzHu\Receipt_**

## Contract types

Contract | Namespace
--- | ---
ArrayableItem | SzuniSoft\SzamlazzHu\Contracts\ArrayableItem
ArrayableItemCollection | SzuniSoft\SzamlazzHu\Contracts\ArrayableItemCollection
ArrayablePayment | SzuniSoft\SzamlazzHu\Contracts\ArrayablePayment
ArrayablePaymentCollection | SzuniSoft\SzamlazzHu\Contracts\ArrayablePaymentCollection

## Initialize receipt

Receipt needs to have **at least** one **item** and **payment**.

```php
use SzuniSoft\SzamlazzHu\Receipt;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;

$receipt = new Receipt();

/*
 * Required attributes
 */
 
// Supported: transfer, cash, bank_card, credit_card, check, c.o.d., 
// gift_card, barter, Borgun, group, EP_card, OTP_simple, compensation, coupon, 
// PayPal, PayU, SZÉP_card, free_of_charge, voucher
$receipt->paymentMethod = PaymentMethods::$paymentMethods['bank_card']; 
$receipt->currency = 'EUR';
$receipt->prefix = 'RCPT';
$receipt->orderNumber = 'some_order_number';

/*
 * Required when currency is not in HUF or Ft 
 */
$receipt->exchangeRateBank = 'Some bank';
$receipt->exchangeRate = 55;
```

#### Note on exchange rate bank
If the currency is foreign (differs from HUF or Ft) you have to specify the currency exchange bank and rate. But if the provided exchange bank equals to "**MNB**" _(Magyar Nemzeti Bank)_ the API will find out the rate automatically.

## Adding items to receipt
#### Add item as array
You need to add at least one item to the receipt.

```php
$receipt->addItem([
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
    'totalNetPrice' => 'automatically calculated..'
]);
```

#### Add item as ArrayableItem contract
You may add items as classes that implements ArrayableItem contract

```php
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItem;

class ThisIsMyProductClass implements ArrayableItem {

    public function toItemCollectionArray() {
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
$receipt->addItems($thisIsMyProduct);
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
$receipt->addItems($cart);
```

## Adding payments to receipt
#### Add payment as array
You need to add at least one payment to the receipt.

```php
$receipt->addPayment([
    // Required
    'paymentMethod' => PaymentMethods::$paymentMethods['bank_card'],
    'amount' => 10,
    // Optional
    'comment' => 'Paid nicely'
]);
```

#### Add payments as ArrayablePayment contract
You may add payments as classes that implements ArrayablePayment contract

```php
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItem;

class ThisIsMyPaymentClass implements ArrayableItem {

    public function toPaymentArray() {
        return [
            'paymentMethod' => PaymentMethods::$paymentMethods['bank_card'],
            'amount' => 10,
            'comment' => 'Paid nicely'
        ];
    }

}
```
```php
$thisIsMyPayment = new ThisIsMyPaymentClass();
$receipt->addPayment($thisIsMyPayment);
```

#### Add multiple payments at once via ArrayablePaymentCollection
Method needs to return with payments wrapped in array or **_ArrayablePayment_** contracts in an array.
```php
use SzuniSoft\SzamlazzHu\Contracts\ArrayablePaymentCollection;

class Payments implemenst ArrayablePaymentCollection {

    protected $payments = [];

    public function __construct() {
        $this->payments = [
            new ThisIsMyPaymentClass(),
            new ThisIsMyPaymentClass(),
            // ...
        ];
    }
    
    public function toPaymentCollectionArray() {
        return $this->payments;
    };
}
```
```php
$payments = new Payments();
$receipt->addPayments($payments);
```

## Saving receipts
```php
$withoutPdf = false; // You can override PDF auto saving.
$receipt->save($withoutPdf);
```

The following attributes are accessible after saving:
```php
$receipt->callId;
$receipt->receiptNumber;
$receipt->createdAt;
$receipt->isCancelled;
```

## Query receipts
### Get receipt (by instance)
```php
$receipt = new Receipt(['receiptNumber' => 'RCPT-123');
$receipt = $client->getReceipt($receipt); // Returns with instance of Receipt or null if not found.
```

### Get receipt with failure (by instance)
```php
$receipt = new Receipt(['receiptNumber' => 'RCPT-123');
try {
    $receipt = $client->getReceipt($receipt);
} catch(ReceiptNotFoundException $exception) {
    // Ops, receipt not found!
}
```

### Get receipt (by receipt number)
```php
$receiptNumber = 'RCPT-123';
$receipt = $client->getReceiptByReceiptNumber($receiptNumber); // Returns with instance of Receipt or null if not found.
```

### Get receipt with failure (by receipt number)
```php
$receiptNumber = 'RCPT-123';
try {
    $receipt = $client->getReceiptByReceiptNumberOrFail($receiptNumber);
} catch(ReceiptNotFoundException $exception) {
    // Ops, receipt not found!
}
```

## Cancelling receipt (Reverse)
```php
$receipt->cancel($withoutPdf = false);
$cancellationReceipt = $receipt->getCancellationReceipt();
$cancellationReceipt->receiptNumber; // Highly recommended to save
```
### Important! Note on cancellation receipts when reversing
**Cancellation receipt can be only obtained during the cancellation process. This is because the official API does not provide a way to obtain the cancellation receipt from the original one. Please make sure you are saving the cancellation receipt number if you would like to access it later!**

If you will try to obtain the cancellation receipt from the original one without the cancellation context the **_CancellationReceiptObtainException_** exception will be thrown.

### Access original receipt by cancellation receipt
In the other hand you can access the original receipt by the cancellation receipt anytime.

```php
$cancellationReceiptNumber = 'RCPT-1234';
$cancellationReceipt = $client->getReceiptByReceiptNumber($cancellationReceiptNumber);
$originalReceipt = $cancellationReceipt->getOriginalReceipt();
// Work with original receipt..
```

## Updating receipt instance
You can update the state and attributes of a receipt instance. This method will update the receipt model.
You can also use this method for saving unsaved PDF files.
```php
$receipt->update($withoutPdf = false);
```