<?php


namespace zoparga\SzamlazzHu\Tests;


use Mockery;
use zoparga\SzamlazzHu\Client\Client;
use zoparga\SzamlazzHu\Client\Models\ReceiptCancellationResponse;
use zoparga\SzamlazzHu\Invoice;
use zoparga\SzamlazzHu\ProformaInvoice;
use zoparga\SzamlazzHu\Receipt;

class ModelClientInteractionTest extends \Orchestra\Testbench\TestCase {


    /**
     * @var Mockery\MockInterface
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(Client::class);
    }

    /**
     * @return Invoice
     */
    protected function createInvoice()
    {
        return (new Invoice())->setClient($this->client);
    }

    /**
     * @return ProformaInvoice
     */
    protected function createProformaInvoice()
    {
        return (new ProformaInvoice())->setClient($this->client);
    }

    /**
     * @return Receipt
     */
    protected function createReceipt()
    {
        return (new Receipt())->setClient($this->client);
    }


    public function test_invoice_save_passes_the_right_default_arguments_to_client()
    {
        $invoice = $this->createInvoice();

        $this->client->shouldReceive('uploadInvoice')
            ->with($invoice, false, null, null)
            ->once()
            ->andReturn(null);

        $invoice->save();
    }


    public function test_invoice_save_passes_the_right_arguments_to_client()
    {
        $invoice = $this->createInvoice();

        $this->client->shouldReceive('uploadInvoice')
            ->with($invoice, true, 'Subject', 'Message')
            ->once()
            ->andReturn(null);

        $invoice->save(true, 'Subject', 'Message', $response);

    }


    public function test_invoice_cancel_passes_the_right_default_arguments_to_client()
    {
        $invoice = $this->createInvoice();

        $mockedResponse = Mockery::mock();
        $mockedResponse->cancellationInvoiceNumber = 123;

        $this->client->shouldReceive('cancelInvoice')
            ->with($invoice, false, null, null)
            ->once()
            ->andReturn($mockedResponse);

        $invoice->cancel();
    }


    public function test_invoice_cancel_passes_the_right_arguments_to_client()
    {
        $invoice = $this->createInvoice();

        $mockedResponse = Mockery::mock();
        $mockedResponse->cancellationInvoiceNumber = 123;

        $this->client->shouldReceive('cancelInvoice')
            ->with($invoice, true, 'Subject', 'Message')
            ->once()
            ->andReturn($mockedResponse);

        $invoice->cancel(true, 'Subject', 'Message', $response);
    }



    public function test_proforma_invoice_save_passes_the_right_default_arguments_to_client()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('uploadProFormaInvoice')
            ->with($proformaInvoice, false, null, null)
            ->once()
            ->andReturn(null);

        $proformaInvoice->save();
    }


    public function test_proforma_invoice_save_passes_the_right_arguments_to_client()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('uploadProFormaInvoice')
            ->with($proformaInvoice, true, 'Subject', 'Message')
            ->once()
            ->andReturn(null);

        $proformaInvoice->save(true, 'Subject', 'Message', $response);
    }


    public function test_proforma_invoice_delete_passes_the_right_arguments_to_client()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('deleteProFormaInvoice')
            ->with($proformaInvoice)
            ->once()
            ->andReturn(null);

        $proformaInvoice->delete();
    }


    public function test_invoice_update_interacts_with_client_well()
    {
        $invoice = $this->createInvoice();

        $this->client->shouldReceive('getInvoice')
            ->with($invoice)
            ->once()
            ->andReturn(null);

        $invoice->update();
    }


    public function test_proforma_invoice_update_interacts_with_client_well()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('getProformaInvoice')
            ->with($proformaInvoice)
            ->once()
            ->andReturn(null);

        $proformaInvoice->update();
    }


    public function test_receipt_update_interacts_with_client_well()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('getReceipt')
            ->with($receipt, false)
            ->once()
            ->andReturn(null);

        $receipt->update();
    }


    public function test_receipt_update_interacts_with_client_with_the_right_arguments()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('getReceipt')
            ->with($receipt, true)
            ->once()
            ->andReturn(null);

        $receipt->update(true);
    }


    public function test_receipt_save_passes_the_right_default_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('uploadReceipt')
            ->with($receipt, false)
            ->once()
            ->andReturn(null);

        $receipt->save();
    }


    public function test_receipt_save_passes_the_right_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('uploadReceipt')
            ->with($receipt, true)
            ->once()
            ->andReturn(null);

        $receipt->save(true, $response);
    }


    public function test_receipt_cancel_passes_the_right_default_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $apiResponse = new \zoparga\SzamlazzHu\Tests\Client\Fixtures\ReceiptCancellationResponse();

        $response = new ReceiptCancellationResponse(
            $receipt,
            $this->client,
            $apiResponse
        );

        $this->client->shouldReceive('cancelReceipt')
            ->with($receipt, false)
            ->once()
            ->andReturn($response);

        $receipt->cancel();
    }


    public function test_receipt_cancel_passes_the_right_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $apiResponse = new \zoparga\SzamlazzHu\Tests\Client\Fixtures\ReceiptCancellationResponse();

        $response = new ReceiptCancellationResponse(
            $receipt,
            $this->client,
            $apiResponse
        );

        $this->client->shouldReceive('cancelReceipt')
            ->with($receipt, true)
            ->once()
            ->andReturn($response);

        $receipt->cancel(true, $resultResponse);
    }

}
