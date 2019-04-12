<?php


namespace SzuniSoft\SzamlazzHu\Tests;


use Mockery;
use SzuniSoft\SzamlazzHu\Client\Client;
use SzuniSoft\SzamlazzHu\Client\Models\ReceiptCancellationResponse;
use SzuniSoft\SzamlazzHu\Invoice;
use SzuniSoft\SzamlazzHu\ProformaInvoice;
use SzuniSoft\SzamlazzHu\Receipt;

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

    /** @test */
    public function invoice_save_passes_the_right_default_arguments_to_client()
    {
        $invoice = $this->createInvoice();

        $this->client->shouldReceive('uploadInvoice')
            ->with($invoice, false, null, null)
            ->once()
            ->andReturn(null);

        $invoice->save();
    }

    /** @test */
    public function invoice_save_passes_the_right_arguments_to_client()
    {
        $invoice = $this->createInvoice();

        $this->client->shouldReceive('uploadInvoice')
            ->with($invoice, true, 'Subject', 'Message')
            ->once()
            ->andReturn(null);

        $invoice->save(true, 'Subject', 'Message', $response);

    }

    /** @test */
    public function invoice_cancel_passes_the_right_default_arguments_to_client()
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

    /** @test */
    public function invoice_cancel_passes_the_right_arguments_to_client()
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


    /** @test */
    public function proforma_invoice_save_passes_the_right_default_arguments_to_client()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('uploadProFormaInvoice')
            ->with($proformaInvoice, false, null, null)
            ->once()
            ->andReturn(null);

        $proformaInvoice->save();
    }

    /** @test */
    public function proforma_invoice_save_passes_the_right_arguments_to_client()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('uploadProFormaInvoice')
            ->with($proformaInvoice, true, 'Subject', 'Message')
            ->once()
            ->andReturn(null);

        $proformaInvoice->save(true, 'Subject', 'Message', $response);
    }

    /** @test */
    public function proforma_invoice_delete_passes_the_right_arguments_to_client()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('deleteProFormaInvoice')
            ->with($proformaInvoice)
            ->once()
            ->andReturn(null);

        $proformaInvoice->delete();
    }

    /** @test */
    public function invoice_update_interacts_with_client_well()
    {
        $invoice = $this->createInvoice();

        $this->client->shouldReceive('getInvoice')
            ->with($invoice)
            ->once()
            ->andReturn(null);

        $invoice->update();
    }

    /** @test */
    public function proforma_invoice_update_interacts_with_client_well()
    {
        $proformaInvoice = $this->createProformaInvoice();

        $this->client->shouldReceive('getProformaInvoice')
            ->with($proformaInvoice)
            ->once()
            ->andReturn(null);

        $proformaInvoice->update();
    }

    /** @test */
    public function receipt_update_interacts_with_client_well()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('getReceipt')
            ->with($receipt, false)
            ->once()
            ->andReturn(null);

        $receipt->update();
    }

    /** @test */
    public function receipt_update_interacts_with_client_with_the_right_arguments()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('getReceipt')
            ->with($receipt, true)
            ->once()
            ->andReturn(null);

        $receipt->update(true);
    }

    /** @test */
    public function receipt_save_passes_the_right_default_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('uploadReceipt')
            ->with($receipt, false)
            ->once()
            ->andReturn(null);

        $receipt->save();
    }

    /** @test */
    public function receipt_save_passes_the_right_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $this->client->shouldReceive('uploadReceipt')
            ->with($receipt, true)
            ->once()
            ->andReturn(null);

        $receipt->save(true, $response);
    }

    /** @test */
    public function receipt_cancel_passes_the_right_default_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $apiResponse = new \SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\ReceiptCancellationResponse();

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

    /** @test */
    public function receipt_cancel_passes_the_right_arguments_to_client()
    {
        $receipt = $this->createReceipt();

        $apiResponse = new \SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\ReceiptCancellationResponse();

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
