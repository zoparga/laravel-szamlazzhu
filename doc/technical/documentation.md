# Technical documentation
## Configuration
Check out configuration [documentation](config.md)

## Usage
### The client
The client is accessible the following ways:
```php
// Resolve by class
$client = app(\SzuniSoft\SzamlazzHu\Client\Client::class);

// Resolve by alias
$client = app('szamlazz-hu.client');
```
Or you can use dependency injection.

### Invoice
[Invoice and proforma invoice documentation](invoice.md)

### Receipt
[Receipt documentation](receipt.md)

## Error handling
The following exceptions may be thrown by the client.
### API Errors
Most of the API errors are converted into exceptions.

Exception class | Error code  
--- | :---:
RemoteMaintenanceException | 1
AuthenticationException | 3 
KeystoreOpeningException | 49
CannotCreateInvoiceException | 54
NoXmlFileException | 53
InvoiceNotificationSendingException | 56
UnsuccessfulInvoiceSignatureException | 55
XmlReadingException | 57
InvalidInvoicePrefixException | 202
InvalidGrossPriceValueException | 261, 264
InvalidNetPriceValueException | 259, 262
ReceiptAlreadyExistsException | 338
ReceiptNotFoundException | 339
InvalidVatRateValueException | 260, 263

### Client Errors

#### InvalidClientConfigurationException
The provided configuration is invalid and cannot be used to initialize agent client.

#### InvoiceNotFoundException
Querying invoice failed. The desired invoice could not be resolved.

#### InvoiceValidationException
The invoice failed against a validation inside the client.
For further details and error messages you can access the Illuminate validator of exception.

#### ReceiptValidationException
The receipt failed against a validation inside the client.
For further details and error messages you can access the Illuminate validator of exception.

#### UnknownOrderIdException
Proforma invoice can access to it's order invoice when order number is provided on it.
If not this exception will be thrown.