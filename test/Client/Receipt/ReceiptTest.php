<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client\Receipt;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SzuniSoft\SzamlazzHu\Client\Errors\ReceiptValidationException;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;
use SzuniSoft\SzamlazzHu\Receipt;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\ReceiptCancellationResponse;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\ReceiptCreationResponse;
use SzuniSoft\SzamlazzHu\Tests\Client\Fixtures\ReceiptRetrievalResponse;

class ReceiptTest extends TestCase {

    /** @test */
    public function it_can_create_receipt()
    {
        $receiptDisk = 'disk';
        $receiptPath = 'receipts';
        $pdfContent = '123';
        $client = $this->client(new ReceiptCreationResponse(base64_encode($pdfContent)), [
            'storage' => [
                'auto_save' => true,
                'disk' => $receiptDisk,
                'path' => $receiptPath
            ]
        ]);
        $receipt = $this->getReceipt(1, $client);

        Storage::fake($receiptDisk);

        /**  @var \SzuniSoft\SzamlazzHu\Client\Models\ReceiptCreationResponse $response */
        $receipt->save(false, $response);

        $this->assertEquals('NYGT-2017-123', $response->receiptNumber);
        $this->assertInstanceOf(Carbon::class, $response->createdAt);
        $this->assertEquals($pdfContent, base64_decode($response->pdfBase64));

        Storage::disk($receiptDisk)->assertExists("$receiptPath/NYGT-2017-123.pdf");
    }

    /** @test */
    public function it_can_create_receipt_without_pdf()
    {
        $receiptDisk = 'disk';
        $receiptPath = 'receipts';
        $pdfContent = '123';
        $client = $this->client(new ReceiptCreationResponse(base64_encode($pdfContent)), [
            'storage' => [
                'auto_save' => false,
                'disk' => $receiptDisk,
                'path' => $receiptPath
            ]
        ]);
        $receipt = $this->getReceipt(1, $client);

        Storage::fake($receiptDisk);

        /**  @var \SzuniSoft\SzamlazzHu\Client\Models\ReceiptCreationResponse $response */
        $receipt->save(false, $response);

        $this->assertEquals('NYGT-2017-123', $response->receiptNumber);
        $this->assertInstanceOf(Carbon::class, $response->createdAt);
        $this->assertEquals($pdfContent, base64_decode($response->pdfBase64));

        Storage::disk($receiptDisk)->assertMissing("$receiptPath/NYGT-2017-123.pdf");
    }

    /** @test */
    public function it_does_not_accept_empty_receipts()
    {
        $this->expectException(ReceiptValidationException::class);
        $this->client()->uploadReceipt(new Receipt());
    }

    /** @test */
    public function it_requires_exchange_rate_and_bank_for_currencies_differs_from_hungarian()
    {
        $client = $this->client([], [], $this->merchant());

        try {
            $receipt = $this->getReceipt(null, $client);
            $receipt->orderNumber = 1;
            $receipt->prefix = 'PRFX';
            $receipt->paymentMethod = PaymentMethods::$paymentMethods['c.o.d.'];
            $receipt->currency = 'EUR';
            $receipt->exchangeRateBank = null;
            $receipt->exchangeRate = null;
            $receipt->save();
        } catch (ReceiptValidationException $exception) {
            $messageBag = $exception->getValidator()->getMessageBag();
            $messages = $messageBag->getMessages();

            $this->assertArrayHasKey('exchangeRateBank', $messages);
            $this->assertArrayHasKey('exchangeRate', $messages);
            $this->assertCount(1, $messages['exchangeRateBank']);
            $this->assertCount(1, $messages['exchangeRate']);
            $this->assertMatchesRegularExpression('/^.*?field is required unless currency is in Ft.*?$/', $messages['exchangeRateBank'][0]);
            $this->assertMatchesRegularExpression('/^.*?field is required unless exchange rate bank is in MNB, currency, Ft, currency, HUF.*?$/', $messages['exchangeRate'][0]);
        }
    }

    /** @test */
    public function it_can_update_pdf_file_when_retrieved()
    {
        $receiptDisk = 'disk';
        $receiptPath = 'receipts';
        $receiptNumber = 'NYGT-2017-123';

        $client = $this->client(new ReceiptRetrievalResponse(123), [
            'storage' => [
                'auto_save' => true,
                'disk' => $receiptDisk,
                'path' => $receiptPath
            ]
        ]);
        $receipt = $this->getReceipt(1, $client);
        $receipt->fill(['receiptNumber' => $receiptNumber]);

        Storage::fake($receiptDisk);

        $client->getReceiptByReceiptNumberOrFail($receiptNumber);

        Storage::disk($receiptDisk)->assertExists("$receiptPath/$receiptNumber.pdf");
    }

    /** @test */
    public function it_can_update_without_pdf()
    {
        $receiptDisk = 'disk';
        $receiptPath = 'receipts';
        $receiptNumber = 'NYGT-2017-123';

        $client = $this->client(new ReceiptRetrievalResponse(), [
            'storage' => [
                'auto_save' => false
            ]
        ]);
        $receipt = $this->getReceipt(1, $client);
        $receipt->fill(['receiptNumber' => $receiptNumber]);

        Storage::fake($receiptDisk);

        $client->getReceiptByReceiptNumberOrFail($receiptNumber);

        Storage::disk($receiptDisk)->assertMissing("$receiptPath/$receiptNumber.pdf");
    }

    /** @test */
    public function it_can_cancel_receipt()
    {
        $pdfContent = 123;
        $receiptDisk = 'disk';
        $receiptPath = 'receipts';
        $receiptNumber = 'NYGT-2017-123';
        $client = $this->client(new ReceiptCancellationResponse($pdfContent), [
            'storage' => [
                'auto_save' => true,
                'disk' => $receiptDisk,
                'path' => $receiptPath
            ]
        ]);
        $receipt = $this->getReceipt(1, $client);
        $receipt->fill(['receiptNumber' => $receiptNumber]);

        Storage::fake($receiptDisk);

        /**  @var \SzuniSoft\SzamlazzHu\Client\Models\ReceiptCancellationResponse $response */
        $result = $receipt->cancel(false, $response);

        $this->assertSame($result, $receipt);
        $this->assertEquals($pdfContent, base64_decode($response->pdfBase64));
        $this->assertEquals($receiptNumber, $response->newReceiptNumber);
        $this->assertEquals('NYGT-2017-100', $response->originalReceiptNumber);

        Storage::disk($receiptDisk)->assertExists("$receiptPath/$response->originalReceiptNumber.pdf");
    }

    /** @test */
    public function it_can_cancel_receipt_without_pdf()
    {
        $pdfContent = 123;
        $receiptDisk = 'disk';
        $receiptPath = 'receipts';
        $receiptNumber = 'NYGT-2017-123';
        $client = $this->client(new ReceiptCancellationResponse($pdfContent), [
            'storage' => [
                'auto_save' => false
            ]
        ]);
        $receipt = $this->getReceipt(1, $client);
        $receipt->fill(['receiptNumber' => $receiptNumber]);

        Storage::fake($receiptDisk);

        /**  @var \SzuniSoft\SzamlazzHu\Client\Models\ReceiptCancellationResponse $response */
        $receipt->cancel(true, $response);
        Storage::disk($receiptDisk)->assertMissing("$receiptPath/$response->originalReceiptNumber.pdf");
    }

}
