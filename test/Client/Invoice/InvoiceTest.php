<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client\Invoice;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\Client\Errors\InvoiceValidationException;
use SzuniSoft\SzamlazzHu\Client\Errors\UnknownOrderIdException;
use SzuniSoft\SzamlazzHu\Client\Models\InvoiceCancellationResponse;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;
use SzuniSoft\SzamlazzHu\Invoice;
use SzuniSoft\SzamlazzHu\ProformaInvoice;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\InvoiceCancellation\InvoiceCancellationPdfResponse;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\InvoiceCancellation\InvoiceCancellationXmlResponse;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\InvoiceCreationResponse;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\InvoiceRetrievalResponse;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\ProformaInvoiceDeletionResponse;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\ProformaInvoiceRetrievalResponse;

class InvoiceTest extends TestCase {

    protected function asd()
    {

        $invoice = new Invoice();
        $invoice->orderNumber = 1;
        PaymentMethods::$paymentMethods;
    }

    /**
     * @param Client $client
     * @return Invoice
     */
    protected function getEmptyInvoiceWithClient(Client $client)
    {

        //$this->client()->

        return $this->getEmptyInvoice(null, $client);
    }

    /**
     * @param null $number
     * @param Client|null $client
     * @param null $items
     * @return Invoice
     */
    protected function getInvoice($number = null, Client $client = null, $items = null)
    {
        $invoice = $this->getEmptyInvoice($number, $client);

        $invoice->fulfillmentAt = Carbon::now();
        $invoice->paymentDeadline = Carbon::now()->addDays(30);
        $invoice->paymentMethod = PaymentMethods::$paymentMethods['bank_card'];
        $invoice->exchangeRateBank = 'bank';
        $invoice->exchangeRate = 0;

        $invoice->setMerchant($this->merchant());
        $invoice->setCustomer($this->customer());

        $items = $items ?: [
            'name' => 'Test', // Name of the item | Required
            'quantity' => 1, // Quantity of this ordered item | Required
            'quantityUnit' => 'piece', // Unit type of quantity | Required
            'netUnitPrice' => 100, // The unit net price of item | Required
            'taxRate' => 27, // The percentage of tax rate | Required
        ];

        $invoice->addItem($items);

        return $invoice;
    }

    /**
     * @param null $number
     * @param Client|null $client
     * @param null $items
     * @return ProformaInvoice
     */
    protected function getProformaInvoice($number = null, Client $client = null, $items = null)
    {
        $proformaInvoice = new ProformaInvoice($this->getEmptyInvoice($number));

        $proformaInvoice->fulfillmentAt = Carbon::now();
        $proformaInvoice->paymentDeadline = Carbon::now()->addDays(30);
        $proformaInvoice->paymentMethod = PaymentMethods::$paymentMethods['bank_card'];
        $proformaInvoice->exchangeRateBank = 'bank';
        $proformaInvoice->exchangeRate = 0;

        $proformaInvoice->setMerchant($this->merchant());
        $proformaInvoice->setCustomer($this->customer());

        if ($client) {
            $proformaInvoice->setClient($client);
        }

        $items = $items ?: [
            'name' => 'Test', // Name of the item | Required
            'quantity' => 1, // Quantity of this ordered item | Required
            'quantityUnit' => 'piece', // Unit type of quantity | Required
            'netUnitPrice' => 100, // The unit net price of item | Required
            'taxRate' => 27, // The percentage of tax rate | Required
        ];

        $proformaInvoice->addItem($items);

        return $proformaInvoice;
    }

    /** @test */
    public function it_does_not_accept_empty_invoices()
    {
        $client = $this->client([], [], $this->merchant());
        $invoice = new Invoice();
        $invoice->setClient($client);

        $this->expectException(InvoiceValidationException::class);
        $invoice->save();
    }

    /** @test */
    public function it_requires_exchange_rate_and_bank_for_currencies_differs_from_hungarian()
    {
        $client = $this->client([], [], $this->merchant());

        /*
         * Invoice is empty
         * */
        try {
            $invoice = $this->getInvoice(null, $client);
            $invoice->orderNumber = 1;
            $invoice->exchangeRateBank = null;
            $invoice->exchangeRate = null;
            $invoice->save();
        } catch (InvoiceValidationException $exception) {
            $messageBag = $exception->getValidator()->getMessageBag();
            $messages = $messageBag->getMessages();

            $this->assertArrayHasKey('exchangeRateBank', $messages);
            $this->assertArrayHasKey('exchangeRate', $messages);
            $this->assertCount(1, $messages['exchangeRateBank']);
            $this->assertCount(1, $messages['exchangeRate']);
            $this->assertMatchesRegularExpression('/^.*?field is required unless currency is in Ft, currency, HUF.*?$/', $messages['exchangeRateBank'][0]);
            $this->assertMatchesRegularExpression('/^.*?field is required unless exchange rate bank is in MNB, currency, Ft, currency, HUF.*?$/', $messages['exchangeRate'][0]);
        }
    }

    /** @test */
    public function it_can_ignore_pdf_file_creation_for_saving_when_desired()
    {

        $invoiceDisk = 'special-disk-for-saving-invoice-pdf-files';
        $savePath = 'invoices';
        $pdfContent = 'some pdf content here..';

        $orderNumber = 1;
        $invoiceNumber = 'XXX-' . date('Y') . '-1';
        $netPrice = 30000;
        $grossPrice = 38100;
        $paymentUrl = 'https://www.google.com';

        $client = $this->client(new InvoiceCreationResponse(true, $invoiceNumber, $netPrice, $grossPrice, $paymentUrl, $pdfContent), [
            'storage' => [
                'auto_save' => true,
                'disk' => $invoiceDisk,
                'path' => $savePath
            ]
        ]);

        $this->assertEquals([
            'disk' => $invoiceDisk, 'path' => $savePath, 'auto_save' => true
        ], $client->getConfig()['storage'], 'Configuration mismatch');

        $invoice = $this->getInvoice($invoiceNumber, $client);
        $invoice->setMerchant($this->merchant());
        $invoice->orderNumber = $orderNumber;

        Storage::fake($invoiceDisk);
        try {
            $invoice->save(true);
        } catch(InvoiceValidationException $exception) {
            dd($exception->getValidator()->getMessageBag()->getMessages());
        }
        Storage::disk($invoiceDisk)->assertMissing("$savePath/$invoiceNumber.pdf");
    }

    /** @test */
    public function it_can_save_new_invoice_pdf()
    {

        $invoiceDisk = 'special-disk-for-saving-invoice-pdf-files';
        $savePath = 'invoices';
        $pdfContent = 'some pdf content here..';

        $orderNumber = 1;
        $invoiceNumber = 'XXX-' . date('Y') . '-1';
        $netPrice = 30000;
        $grossPrice = 38100;
        $paymentUrl = 'https://www.google.com';

        $client = $this->client(new InvoiceCreationResponse(true, $invoiceNumber, $netPrice, $grossPrice, $paymentUrl, $pdfContent), [
            'storage' => [
                'auto_save' => true,
                'disk' => $invoiceDisk,
                'path' => $savePath
            ]
        ]);

        $this->assertEquals([
            'disk' => $invoiceDisk, 'path' => $savePath, 'auto_save' => true
        ], $client->getConfig()['storage'], 'Configuration mismatch');

        $invoice = $this->getInvoice($invoiceNumber, $client);
        $invoice->orderNumber = $orderNumber;

        Storage::fake($invoiceDisk);

        $invoice->save();

        Storage::disk($invoiceDisk)->assertExists("$savePath/$invoiceNumber.pdf");
        $this->assertEquals(Storage::disk($invoiceDisk)->get("$savePath/$invoiceNumber.pdf"), $pdfContent, 'Saved PDF file has invalid content!');
    }

    /** @test */
    public function it_can_create_invoice_and_offers_response()
    {

        $pdfContent = 'some pdf content here..';

        $orderNumber = 1;
        $invoiceNumber = 'XXX-' . date('Y') . '-1';
        $netPrice = 30000;
        $grossPrice = 38100;
        $paymentUrl = 'https://www.google.com';

        $client = $this->client(new InvoiceCreationResponse(true, $invoiceNumber, $netPrice, $grossPrice, $paymentUrl, $pdfContent));

        $invoice = $this->getInvoice($invoiceNumber, $client);
        $invoice->orderNumber = $orderNumber;

        $result = $invoice->save(false, null, null, $response);

        $this->assertSame($invoice, $result);
        $this->assertEquals(base64_decode($response->pdfBase64), $pdfContent, 'PDF content could not be resolved by base64 processing');
        $this->assertEquals($response->paymentUrl, $paymentUrl, 'Payment URL is unknown');
        $this->assertEquals($response->netPrice, $netPrice, 'Net price is not retrieved');
        $this->assertEquals($response->grossPrice, $grossPrice, 'Gross price is not retrieved');
        $this->assertEquals($response->invoiceNumber, $invoiceNumber, 'Invoice number has been modified');
    }

    /** @test */
    public function it_can_obtain_invoice()
    {

        $client = $this->client(new InvoiceRetrievalResponse());

        $invoice = $client->getInvoice('E-LOLO-66');

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertTrue($invoice->isElectronic, 'Electronic flag is wrong');
        $this->assertEquals($invoice->invoiceLanguage, 'hu', 'Invoice language is wrong');
        $this->assertEquals($invoice->currency, 'HUF', 'Currency is wrong');
        $this->assertInstanceOf(Carbon::class, $invoice->createdAt, 'Created at timestamp should be instance of Carbon');
        $this->assertInstanceOf(Carbon::class, $invoice->paymentDeadline, 'Deadline timestamp should be instance of Carbon');
        $this->assertFalse($invoice->isImprestInvoice);
        $this->assertFalse($invoice->isFinalInvoice, 'Is final invoice flag is wrong.');
        $this->assertFalse($invoice->isPaid, 'Is paid flag is wrong.');
        $this->assertEquals('E-LOLO-66', $invoice->invoiceNumber, 'Invoice number retrieval failed.');
        $this->assertEquals('bank_card', $invoice->paymentMethod, 'Unrecognized payment method.');
        $this->assertNull($invoice->exchangeRateBank);
        $this->assertEquals($invoice->exchangeRate, 0);
        $this->assertTrue($invoice->isKata);

        $this->assertEquals('Merchant', $invoice->merchantName, 'Wrong merchant name');
        $this->assertEquals('Magyarország', $invoice->merchantCountry, 'Wrong merchant country');
        $this->assertEquals('Budapest', $invoice->merchantCity, 'Wrong merchant city');
        $this->assertEquals('1086', $invoice->merchantZipCode, 'Wrong merchant zip code');
        $this->assertEquals('Some street', $invoice->merchantAddress, 'Wrong merchant address');
        $this->assertEquals('123', $invoice->merchantTaxNumber, 'Wrong merchant tax number');
        $this->assertEquals('123', $invoice->merchantEuTaxNumber, 'Wrong merchant EU tax number');
        $this->assertEquals('MNB', $invoice->merchantBank, 'Wrong merchant bank name');
        $this->assertEquals('123', $invoice->merchantBankAccountNumber, 'Wrong merchant bank account number');

        $this->assertEquals('Customer', $invoice->customerName);
        $this->assertEquals('Magyarország', $invoice->customerCountry);
        $this->assertEquals('1324', $invoice->customerZipCode);
        $this->assertEquals('Somewhere', $invoice->customerCity);
        $this->assertEquals('1234', $invoice->customerAddress);
        $this->assertNull($invoice->customerTaxNumber);

        $this->assertEquals(1, $invoice->total());
        $item = $invoice->items()->first();
        $this->assertSame([
            'name' => 'Apple',
            'quantity' => 1.0,
            'quantityUnit' => 'kg',
            'netUnitPrice' => 380.0,
            'taxRate' => 20.0,
            'totalNetPrice' => 380.0,
            'taxValue' => 76.0,
            'totalGrossPrice' => 456.0,
            'comment' => 'Healthy food'
        ], $item);
    }

    /** @test */
    public function it_can_cancel_invoice()
    {

        $invoiceDisk = 'such-pdfz';
        $savePath = 'very-invoice';

        $client = $this->client([
            new InvoiceCancellationXmlResponse(200, [], 'xmlagentresponse=DONE;2011-123'),
            new InvoiceCancellationPdfResponse(200, [], '123'),
        ], [
            'storage' => [
                'auto_save' => true,
                'disk' => $invoiceDisk,
                'path' => $savePath
            ]
        ]);

        $invoice = $this->getInvoice()->setClient($client);
        $invoice->invoiceNumber = '9';

        Storage::fake($invoiceDisk);

        /**  @var InvoiceCancellationResponse $response */
        $invoice->cancel(false, null, null, $response);

        $this->assertEquals('2011-123', $response->cancellationInvoiceNumber);
        $this->assertEquals('9', $response->originalInvoice->invoiceNumber);

        Storage::disk($invoiceDisk)->assertExists("$savePath/2011-123.pdf");
        $this->assertEquals(Storage::disk($invoiceDisk)->get("$savePath/2011-123.pdf"), '123');

    }

    /** @test */
    public function it_can_cancel_invoice_without_pdf_creation()
    {

        $invoiceDisk = 'such-pdfz';
        $savePath = 'very-invoice';

        $client = $this->client([
            new InvoiceCancellationXmlResponse(200, [], 'xmlagentresponse=DONE;2011-123'),
        ], [
            'storage' => [
                'auto_save' => true,
                'disk' => $invoiceDisk,
                'path' => $savePath
            ]
        ]);

        $invoice = $this->getInvoice()->setClient($client);
        $invoice->invoiceNumber = '9';

        Storage::fake($invoiceDisk);

        /**  @var InvoiceCancellationResponse $response */
        $invoice->cancel(true, null, null, $response);

        $this->assertEquals('2011-123', $response->cancellationInvoiceNumber);
        $this->assertEquals('9', $response->originalInvoice->invoiceNumber);

        Storage::disk($invoiceDisk)->assertMissing("$savePath/2011-123.pdf");
    }

    /** @test */
    public function it_returns_with_the_right_type_of_invoice_when_using_invoice_number()
    {
        $client = $this->client([
            new InvoiceRetrievalResponse(),
            new ProformaInvoiceRetrievalResponse()
        ]);

        $this->assertInstanceOf(Invoice::class, $client->getInvoice('E-LOLO-66'));
        $this->assertInstanceOf(ProformaInvoice::class, $client->getProformaInvoice('D-LOLO-66'));
    }

    /** @test */
    public function it_can_return_with_dynamic_invoice_type_based_on_order_number()
    {
        $client = $this->client([
            new InvoiceRetrievalResponse(),
            new ProformaInvoiceRetrievalResponse()
        ]);

        $this->assertInstanceOf(Invoice::class, $client->getInvoiceByOrderNumber('123'));
        $this->assertInstanceOf(ProformaInvoice::class, $client->getInvoiceByOrderNumber('123'));
    }

    /** @test */
    public function order_invoice_accessible_from_proforma_invoice()
    {
        $client = $this->client([
            new ProformaInvoiceRetrievalResponse(),
            new InvoiceRetrievalResponse()
        ]);

        $proformaInvoice = $client->getInvoiceByOrderNumber('123');
        $proformaInvoice->setClient($client);

        $orderInvoice = $proformaInvoice->orderInvoice();

        $this->assertInstanceOf(Invoice::class, $orderInvoice);
        $this->assertEquals('123', $orderInvoice->orderNumber);
    }

    /** @test */
    public function it_can_fail_when_no_order_number_specified_on_proforma_invoice()
    {
        $client = $this->client(new ProformaInvoiceRetrievalResponse(''));
        $proformaInvoice = $client->getInvoiceByOrderNumber('123');

        $this->assertInstanceOf(ProformaInvoice::class, $proformaInvoice);
        $this->expectException(UnknownOrderIdException::class);
        $proformaInvoice->orderInvoice();
    }

    /** @test */
    public function invoice_save_offers_response_reference_capture()
    {
        $invoiceNumber = 'XXX-2018-55';
        $client = $this->client(new InvoiceCreationResponse(true, $invoiceNumber, 100, 120, 'https://www.google.com', '123'));
        $invoice = $this->getInvoice($invoiceNumber, $client);
        $invoice->orderNumber = 1;
        $invoice->setClient($client);

        /**  @var \SzuniSoft\SzamlazzHu\Client\Models\InvoiceCreationResponse $response */
        $invoice->save(false, null, null, $response);
        $this->assertInstanceOf(\SzuniSoft\SzamlazzHu\Client\Models\InvoiceCreationResponse::class, $response);
    }

    /** @test */
    public function proforma_invoice_save_offers_response_reference_capture()
    {
        $invoiceNumber = 'D-2018-55';
        $client = $this->client(new InvoiceCreationResponse(true, $invoiceNumber, 100, 120, 'https://www.google.com', '123'));
        $invoice = $this->getProformaInvoice($invoiceNumber, $client);
        $invoice->orderNumber = 1;
        $invoice->setClient($client);

        /**  @var \SzuniSoft\SzamlazzHu\Client\Models\InvoiceCreationResponse $response */
        $result = $invoice->save(false, null, null, $response);
        $this->assertEquals($invoice, $result);
        $this->assertInstanceOf(\SzuniSoft\SzamlazzHu\Client\Models\InvoiceCreationResponse::class, $response);
    }

    /** @test */
    public function it_offers_cancellation_response_when_cancelling_invoice()
    {
        $client = $this->client(new InvoiceCancellationXmlResponse(200, [], 'xmlagentresponse=DONE;2018-123'));
        $invoice = $this->getInvoice('E-2018-123', $client);
        /**  @var InvoiceCancellationResponse $response */
        $result = $invoice->cancel(true, null, null, $response);

        $this->assertEquals($invoice, $result);
        $this->assertInstanceOf(InvoiceCancellationResponse::class, $response);
    }

    /** @test */
    public function it_offers_cancellation_response_when_deleting_proforma_invoice()
    {
        $client = $this->client(new ProformaInvoiceDeletionResponse());
        $proformaInvoice = $this->getProformaInvoice('D-2018-123', $client);

        /**  @var \SzuniSoft\SzamlazzHu\Client\Models\ProformaInvoiceDeletionResponse $response */
        $result = $proformaInvoice->delete($response);

        $this->assertEquals($proformaInvoice, $result);
        $this->assertInstanceOf(\SzuniSoft\SzamlazzHu\Client\Models\ProformaInvoiceDeletionResponse::class, $response);
    }


}
