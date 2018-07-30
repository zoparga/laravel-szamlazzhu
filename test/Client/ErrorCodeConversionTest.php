<?php


namespace SzuniSoft\SzamlazzHu\Tests\Client;


use GuzzleHttp\Psr7\Response;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\AuthenticationException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\CannotCreateInvoiceException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\CommonResponseException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\InvalidGrossPriceValueException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\InvalidInvoicePrefixException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\InvalidNetPriceValueException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\InvalidVatRateValueException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\InvoiceNotificationSendingException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\KeystoreOpeningException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\NoXmlFileException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\RemoteMaintenanceException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\UnsuccessfulInvoiceSignatureException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\XmlReadingException;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;
use SzuniSoft\SzamlazzHu\Receipt;

class ErrorCodeConversionTest extends TestCase {

    /**
     * @param int $orderNumber
     * @param null $items
     * @param null $payments
     * @return \SzuniSoft\SzamlazzHu\Receipt
     */
    protected function getReceipt($orderNumber = 1, $items = null, $payments = null)
    {

        $receipt = new Receipt([
            'orderNumber' => $orderNumber,
            'prefix' => 'PRFX',
            'currency' => 'EUR',
            'comment' => 'nothing special',
            'paymentMethod' => PaymentMethods::$paymentMethods['bank_card'],
            'exchangeRateBank' => 'MNB',
            'exchangeRate' => 300
        ]);

        $payments = $payments ?: [
            'paymentMethod' => PaymentMethods::$paymentMethods['bank_card'],
            'amount' => 127,
            'comment' => 'Paid gracefully'
        ];
        $items = $items ?: [
            'name' => 'Test Product',
            'quantity' => 1,
            'quantityUnit' => 'db',
            'netUnitPrice' => 100,
            'taxRate' => 27,
        ];

        $receipt->addPayment($payments);
        $receipt->addItem($items);

        $receipt->fill(['receiptNumber' => (string)$orderNumber]);
        return $receipt;
    }

    /**
     * @param $code
     * @return Response
     */
    protected function createErrorResponse($code)
    {
        return new Response(200, ['szlahu_error_code' => $code]);
    }

    /**
     * @param $code
     * @throws \SzuniSoft\SzamlazzHu\Client\Errors\ModelValidationException
     */
    protected function invokeReceiptGetter($code)
    {
        $this->client($this->createErrorResponse($code))->getReceipt($this->getReceipt());
    }

    /** @test */
    public function it_can_report_remote_maintenance()
    {
        $this->expectException(RemoteMaintenanceException::class);
        $this->invokeReceiptGetter(1);
    }

    /** @test */
    public function it_can_report_authentication_error()
    {
        $this->expectException(AuthenticationException::class);
        $this->invokeReceiptGetter(3);
    }

    /** @test */
    public function it_can_detect_keystore_opening_failure()
    {
        $this->expectException(KeystoreOpeningException::class);
        $this->invokeReceiptGetter(49);
    }

    /** @test */
    public function it_can_interpret_no_xml_file_failure()
    {
        $this->expectException(NoXmlFileException::class);
        $this->invokeReceiptGetter(53);
    }

    /** @test */
    public function it_can_report_invoice_creation_failure()
    {
        $this->expectException(CannotCreateInvoiceException::class);
        $this->invokeReceiptGetter(54);
    }

    /** @test */
    public function it_can_report_invoice_signature_problem()
    {
        $this->expectException(UnsuccessfulInvoiceSignatureException::class);
        $this->invokeReceiptGetter(55);
    }

    /** @test */
    public function it_can_report_notification_sending_failure()
    {
        $this->expectException(InvoiceNotificationSendingException::class);
        $this->invokeReceiptGetter(56);
    }

    /** @test */
    public function it_can_report_remote_xml_reading_error()
    {
        $this->expectException(XmlReadingException::class);
        $this->invokeReceiptGetter(57);
    }

    /** @test */
    public function it_can_detect_bad_invoice_prefixes()
    {
        $this->expectException(InvalidInvoicePrefixException::class);
        $this->invokeReceiptGetter(202);
    }

    /** @test */
    public function it_can_detect_invalid_net_price_value_error()
    {
        $this->expectException(InvalidNetPriceValueException::class);
        $this->invokeReceiptGetter(259);
    }

    /** @test */
    public function it_can_detect_invalid_vat_rate_value_error()
    {
        $this->expectException(InvalidVatRateValueException::class);
        $this->invokeReceiptGetter(260);
    }

    /** @test */
    public function it_can_detect_invalid_gross_price_value_error()
    {
        $this->expectException(InvalidGrossPriceValueException::class);
        $this->invokeReceiptGetter(261);
    }

    /** @test */
    public function it_can_fall_back_to_common_exception()
    {
        $this->expectException(CommonResponseException::class);
        $this->invokeReceiptGetter(-1);
    }

    /** @test */
    public function it_can_fall_back_to_response_content_error_code()
    {
        $this->expectException(AuthenticationException::class);
        $this->client(
            new Response(200, [],'<sikeres>false</sikeres><hibakod>3</hibakod>')
        )->getReceipt($this->getReceipt());
    }

}