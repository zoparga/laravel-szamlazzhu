<?php


namespace SzuniSoft\SzamlazzHu\Client;


use Carbon\Carbon;
use Closure;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
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
use SzuniSoft\SzamlazzHu\Client\ApiErrors\ReceiptAlreadyExistsException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\ReceiptNotFoundException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\RemoteMaintenanceException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\UnsuccessfulInvoiceSignatureException;
use SzuniSoft\SzamlazzHu\Client\ApiErrors\XmlReadingException;
use SzuniSoft\SzamlazzHu\Client\Errors\InvalidClientConfigurationException;
use SzuniSoft\SzamlazzHu\Client\Errors\InvoiceNotFoundException;
use SzuniSoft\SzamlazzHu\Client\Errors\InvoiceValidationException;
use SzuniSoft\SzamlazzHu\Client\Errors\ModelValidationException;
use SzuniSoft\SzamlazzHu\Client\Errors\ReceiptValidationException;
use SzuniSoft\SzamlazzHu\Client\Models\InvoiceCancellationResponse;
use SzuniSoft\SzamlazzHu\Client\Models\InvoiceCreationResponse;
use SzuniSoft\SzamlazzHu\Client\Models\ProformaInvoiceDeletionResponse;
use SzuniSoft\SzamlazzHu\Client\Models\ReceiptCancellationResponse;
use SzuniSoft\SzamlazzHu\Client\Models\ReceiptCreationResponse;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableMerchant;
use SzuniSoft\SzamlazzHu\Internal\AbstractInvoice;
use SzuniSoft\SzamlazzHu\Internal\AbstractModel;
use SzuniSoft\SzamlazzHu\Internal\Support\ClientAccessor;
use SzuniSoft\SzamlazzHu\Internal\Support\InvoiceValidationRules;
use SzuniSoft\SzamlazzHu\Internal\Support\MerchantHolder;
use SzuniSoft\SzamlazzHu\Internal\Support\NormalizeParsedNumericArrays;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;
use SzuniSoft\SzamlazzHu\Internal\Support\ReceiptValidationRules;
use SzuniSoft\SzamlazzHu\Invoice;
use SzuniSoft\SzamlazzHu\ProformaInvoice;
use SzuniSoft\SzamlazzHu\Receipt;
use SzuniSoft\SzamlazzHu\Util\XmlParser;
use XMLWriter;

class Client
{

    use MerchantHolder,
        PaymentMethods,
        NormalizeParsedNumericArrays,
        InvoiceValidationRules,
        ReceiptValidationRules,
        XmlParser;

    /*
     * All the available actions.
     * */
    private const ACTIONS = [

        // Used for cancelling (existing) invoices
        'CANCEL_INVOICE'          => [
            'name'   => 'action-szamla_agent_st',
            'schema' => [
                /*
                 * Important! Please always note order
                 * */
                'xmlszamlast', // Action name
                'http://www.szamlazz.hu/xmlszamlast', // Namespace
                'http://www.szamlazz.hu/xmlszamlast xmlszamlast.xsd' // Schema location
            ],
        ],

        // Used for deleting (existing) proforma invoices
        'DELETE_PROFORMA_INVOICE' => [
            'name'   => 'action-szamla_agent_dijbekero_torlese',
            'schema' => [
                'xmlszamladbkdel',
                'http://www.szamlazz.hu/xmlszamladbkdel',
                'http://www.szamlazz.hu/xmlszamladbkdel http://www.szamlazz.hu/docs/xsds/szamladbkdel/xmlszamladbkdel.xsd',
            ],
        ],

        // Used for obtaining (both) invoices and proforma invoices
        'GET_COMMON_INVOICE'      => [
            'name'   => 'action-szamla_agent_xml',
            'schema' => [
                'xmlszamlaxml',
                'http://www.szamlazz.hu/xmlszamlaxml',
                'http://www.szamlazz.hu/xmlszamlaxml http://www.szamlazz.hu/docs/xsds/agentpdf/xmlszamlaxml.xsd',
            ],
        ],

        // Used to upload (create) new common and proforma invoice
        'UPLOAD_COMMON_INVOICE'   => [
            'name'   => 'action-xmlagentxmlfile',
            'schema' => [
                'xmlszamla',
                'http://www.szamlazz.hu/xmlszamla',
                'http://www.szamlazz.hu/xmlszamla http://www.szamlazz.hu/docs/xsds/agent/xmlszamla.xsd',
            ],
        ],

        // Used to create / update receipt
        'UPLOAD_RECEIPT'          => [
            'name'   => 'action-szamla_agent_nyugta_create',
            'schema' => [
                'xmlnyugtacreate',
                'http://www.szamlazz.hu/xmlnyugtacreate',
                'http://www.szamlazz.hu/xmlnyugtacreate http://www.szamlazz.hu/docs/xsds/nyugta/xmlnyugtacreate.xsd',
            ],
        ],

        // Cancelling receipt
        'CANCEL_RECEIPT'          => [
            'name'   => 'action-szamla_agent_nyugta_storno',
            'schema' => [
                'xmlnyugtast',
                'http://www.szamlazz.hu/xmlnyugtast',
                'http://www.szamlazz.hu/xmlnyugtast http://www.szamlazz.hu/docs/xsds/nyugtast/xmlnyugtast.xsd',
            ],
        ],

        // Obtaining a single receipt
        'GET_RECEIPT'             => [
            'name'   => 'action-szamla_agent_nyugta_get',
            'schema' => [
                'xmlnyugtaget',
                'http://www.szamlazz.hu/xmlnyugtaget',
                'http://www.szamlazz.hu/xmlnyugtaget http://www.szamlazz.hu/docs/xsds/nyugtaget/xmlnyugtaget.xsd',
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array|null
     */
    protected $defaultMerchant = null;

    /**
     * Default API config
     *
     * @var array
     */
    protected $defaultConfig = [
        'timeout'     => 30,
        'base_uri'    => 'https://www.szamlazz.hu/',
        'certificate' => [
            'enabled' => false,
        ],
        'storage'     => [
            'auto_save' => false,
            'disk'      => 'local',
            'path'      => 'szamlazzhu',
        ],
    ];

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param array                   $config
     * @param \GuzzleHttp\Client      $client
     * @param array|ArrayableMerchant $merchant
     *
     * @throws InvalidClientConfigurationException
     */
    public function __construct(array $config, \GuzzleHttp\Client $client, $merchant = null)
    {
        $this->config = array_merge($this->defaultConfig, $config);
        static::validateConfig($this->config);

        if (!empty($merchant)) {
            $this->defaultMerchant = $this->simplifyMerchant($merchant);
        }

        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @throws InvalidClientConfigurationException
     */
    protected static function validateConfig(array $config)
    {

        $rules = [
            'credentials.username' => 'required_without:credentials.api_key',
            'credentials.password' => 'required_without:credentials.api_key',
            'credentials.api_key'  => 'required_without:credentials.username',
            'certificate.enabled'  => ['required', 'boolean'],
            'certificate'          => ['sometimes', 'array'],
            'certificate.path'     => [
                'required_if:certificate.enabled,1',
                'bail',
                'required_with_all:certificate.disk',
                function ($attribute, $value, $fail) use (&$config) {

                    if (isset($config['certificate'])) {
                        $certificate = $config['certificate'];

                        if ($certificate['enabled'] && isset($certificate['disk'])) {

                            $disk = $config['certificate']['disk'];
                            if (!Storage::disk($disk)->exists($value)) {
                                return $fail("The specified cert file could not be resolved from disk [$disk] at path [$value]!");
                            }
                        }
                    }

                },
            ],
            'timeout'              => ['integer', 'min:10', 'max:300'],
            'base_uri'             => ['url'],
        ];

        if (isset($config['certificate'], $config['certificate']['enabled']) && !!$config['certificate']['enabled']) {
            $rules['certificate.disk'] = ['required'];
        }

        if (($validator = Validator::make($config, $rules))->fails()) {
            throw new InvalidClientConfigurationException($validator);
        }
    }

    /**
     * @return string|null
     */
    protected function getCertificatePath()
    {
        return $this->config['certificate']['enabled']
            ? $this->config['certificate']['path']
            : null;
    }

    /**
     * @return bool
     */
    protected function shouldSavePdf()
    {
        return $this->config['storage']['auto_save'] === true;
    }

    /**
     * @return string
     */
    protected function storageDisk()
    {
        return $this->config['storage']['disk'];
    }

    /**
     * @return string
     */
    protected function storagePath()
    {
        return $this->config['storage']['path'];
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function stringifyBoolean($value)
    {
        return $value
            ? 'true'
            : 'false';
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function commonCurrencyFormat($value)
    {
        return number_format($value, 3, '.', '');
    }

    /**
     * @param XMLWriter $writer
     * @param           $element
     * @param           $content
     */
    protected function writeCdataElement(XMLWriter &$writer, $element, $content)
    {
        $writer->startElement($element);
        $writer->writeCdata($content);
        $writer->endElement();
    }

    /**
     * @param AbstractModel $abstractModel
     * @param array         $rules
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function modelValidator(AbstractModel $abstractModel, array $rules)
    {
        return Validator::make($abstractModel->toApiArray(), $rules);
    }

    /**
     * Validates invoice against the specified rules
     *
     * @param AbstractModel $model
     * @param array         $rules
     *
     * @return bool
     * @throws ReceiptValidationException
     * @throws InvoiceValidationException
     */
    protected function validateModel(AbstractModel $model, array $rules)
    {
        $validator = $this->modelValidator($model, $rules);

        if ($validator->fails()) {

            if ($model instanceof AbstractInvoice) {
                throw new InvoiceValidationException($model, $validator);
            }
            elseif ($model instanceof Receipt) {
                throw new ReceiptValidationException($model, $validator);
            }
        }
        return true;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    protected function isAuthenticationError(ResponseInterface $response)
    {
        try {
            $xml = $this->parse((string)$response->getBody());
            if (isset($xml['sikeres']) && $xml['sikeres'] === 'false'
                && isset($xml['hibauzenet']) && $xml['hibauzenet'] === 'Sikertelen bejelentkezés.') {
                return true;
            }
        }
        catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Converts API error to catchable local exception
     *
     * @param ResponseInterface $response
     *
     * @throws CommonResponseException
     */
    protected function convertResponseToException(ResponseInterface $response)
    {

        $code    = 500;
        $message = 'Unknown error';

        if ($response->hasHeader('szlahu_error_code')) {
            $code = $response->getHeader('szlahu_error_code')[0];
        }
        elseif ($this->isAuthenticationError($response)) {
            $code = 2;
        }
        elseif (preg_match("/<hibakod>([0-9]+)\<\/hibakod>/", (string)$response->getBody(), $matches)) {
            if (isset($matches[1]) && is_numeric($matches[1])) {
                $code = (int)$matches[1];
            }
        }

        if ($response->hasHeader('szlahu_error')) {
            $message = $response->getHeader('szlahu_error')[0];
        }

        $httpStatusCode = $response->getStatusCode();

        $exceptionClass = null;

        switch ((int)$code) {
            case 3:
                $exceptionClass = AuthenticationException::class;
                break;
            case 54:
                $exceptionClass = CannotCreateInvoiceException::class;
                break;
            case 261:
            case 264:
                $exceptionClass = InvalidGrossPriceValueException::class;
                break;
            case 202:
                $exceptionClass = InvalidInvoicePrefixException::class;
                break;
            case 259:
            case 262:
                $exceptionClass = InvalidNetPriceValueException::class;
                break;
            case 338:
                $exceptionClass = ReceiptAlreadyExistsException::class;
                break;
            case 339:
                $exceptionClass = ReceiptNotFoundException::class;
                break;
            case 260:
            case 263:
                $exceptionClass = InvalidVatRateValueException::class;
                break;
            case 56:
                $exceptionClass = InvoiceNotificationSendingException::class;
                break;
            case 49:
                $exceptionClass = KeystoreOpeningException::class;
                break;
            case 53:
                $exceptionClass = NoXmlFileException::class;
                break;
            case 1:
                $exceptionClass = RemoteMaintenanceException::class;
                break;
            case 55:
                $exceptionClass = UnsuccessfulInvoiceSignatureException::class;
                break;
            case 57:
                $exceptionClass = XmlReadingException::class;
                break;
            default:
                throw new CommonResponseException($response, $message ?: 'Unknown error', $httpStatusCode ?: 500);
        }

        if ($exceptionClass) {
            throw new $exceptionClass($response);
        }

    }

    /**
     * Process the response obtained over HTTP
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws CommonResponseException
     */
    protected function processResponse(ResponseInterface $response)
    {
        if ($response->hasHeader('szlahu_error_code') or
            str_contains((string)$response->getBody(), '<sikeres>false</sikeres>')) {
            $this->convertResponseToException($response);
        }

        return $response;
    }

    /**
     * Sends request to Szamlazz.hu server
     *
     * @param string $action
     * @param string $contents
     * @param string $uri
     * @param string $method
     *
     * @return ResponseInterface
     */
    protected function send(string $action, string $contents, $uri = '/szamla/', $method = 'POST')
    {

        $options = [
            'timeout'  => $this->config['timeout'],
            'base_uri' => $this->config['base_uri'],
        ];

        /*
         * Setup certificate if provided
         * */
        if ($certificatePath = $this->getCertificatePath()) {
            $options['cert'] = [$certificatePath];
        }

        /*
         * Inject content body into request
         * */
        if ($action && $contents) {
            $options['multipart'] = [
                [
                    'name'     => $action,
                    'filename' => 'invoice.xml',
                    'contents' => $contents,
                ],
            ];
        };

        return $this->client->requestAsync($method, $uri, $options)
            ->then(function (Response $response) {
                return $this->processResponse($response);
            }, function () {

            })
            ->wait();
    }

    /**
     * @param        $disk
     * @param        $path
     * @param string $pdfContent
     * @param string $as
     *
     * @return bool
     */
    protected function updatePdfFile($disk, $path, $pdfContent, $as)
    {

        $fullPath = $path . "/$as";

        return ($this->shouldSavePdf() && !Storage::disk($disk)->exists($fullPath))
            ? Storage::disk($disk)->put($fullPath, $pdfContent)
            : false;
    }

    /**
     * Writes auth credentials via the given writer
     *
     * @param XMLWriter $writer
     */
    protected function writeCredentials(XMLWriter &$writer)
    {

        if(isset($this->config['credentials']['api_key'])) {

            $writer->writeElement('szamlaagentkulcs', $this->config['credentials']['api_key']);
        }
        else {

            $writer->writeElement('felhasznalo', $this->config['credentials']['username']);
            $writer->writeElement('jelszo', $this->config['credentials']['password']);
        }
    }

    /**
     * @param string $invoiceClass
     * @param array  $head
     * @param array  $customer
     * @param array  $merchant
     * @param array  $items
     *
     * @return AbstractInvoice|ClientAccessor|Invoice|ProformaInvoice
     */
    protected function invoiceFactory($invoiceClass, array $head, array $customer, array $merchant, array $items)
    {
        /**  @var ClientAccessor $invoice */
        $invoice = new $invoiceClass($head, $items, $customer, $merchant);
        $invoice->setClient($this);
        return $invoice;
    }

    /**
     * @param callable|Closure $write
     * @param                  $root
     * @param string           $namespace
     * @param string           $schemaLocation
     *
     * @return string
     */
    protected function writer(
        $write,
        $root,
        $namespace,
        $schemaLocation
    )
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0', 'UTF-8');
        $writer->setIndent(true);

        // Write NS attributes
        $writer->startElementNs(null, $root, $namespace);
        $writer->writeAttributeNs('xsi', 'schemaLocation', null, $schemaLocation);
        $writer->writeAttributeNs('xmlns', 'xsi', null, 'http://www.w3.org/2001/XMLSchema-instance');

        $write($writer);
        $writer->endElement();
        return $writer->outputMemory();
    }

    /**
     * @param Receipt $receipt
     *
     * @return bool
     * @throws ReceiptValidationException|ModelValidationException
     */
    public function validateReceiptForSaving(Receipt $receipt)
    {
        return $this->validateModel($receipt, $this->validationRulesForSavingReceipt());
    }

    /**
     * @param Invoice $invoice
     *
     * @return bool
     * @throws InvoiceValidationException|ModelValidationException
     */
    public function validateInvoiceForSaving(Invoice $invoice)
    {
        return $this->validateModel($invoice, $this->validationRulesForSavingInvoice());
    }

    /**
     * @param ProformaInvoice $invoice
     *
     * @return bool
     * @throws InvoiceValidationException|ModelValidationException
     */
    public function validateProformaInvoiceForSaving(ProformaInvoice $invoice)
    {
        return $this->validateModel($invoice, $this->validationRulesForSavingInvoice());
    }

    /**
     * @param ProformaInvoice $invoice
     * @param bool            $withoutPdf
     * @param null            $emailSubject
     * @param null            $emailMessage
     *
     * @return InvoiceCreationResponse
     * @throws ModelValidationException
     */
    public function uploadProFormaInvoice(ProformaInvoice $invoice, $withoutPdf = false, $emailSubject = null, $emailMessage = null)
    {
        return $this->uploadCommonInvoice($invoice, $withoutPdf, $emailSubject, $emailMessage);
    }

    /**
     * Creates invoice
     *
     * @param Invoice $invoice
     * @param bool    $withoutPdf
     * @param null    $emailSubject
     * @param null    $emailMessage
     *
     * @return InvoiceCreationResponse
     * @throws \SzuniSoft\SzamlazzHu\Client\Errors\ModelValidationException
     */
    public function uploadInvoice(Invoice $invoice, $withoutPdf = false, $emailSubject = null, $emailMessage = null)
    {
        return $this->uploadCommonInvoice($invoice, $withoutPdf, $emailSubject, $emailMessage);
    }

    /**
     * @param AbstractInvoice $invoice
     * @param bool            $withoutPdf
     * @param null            $emailSubject
     * @param null            $emailMessage
     *
     * @return InvoiceCreationResponse
     * @throws ModelValidationException
     */
    protected function uploadCommonInvoice(AbstractInvoice $invoice, $withoutPdf = false, $emailSubject = null, $emailMessage = null)
    {

        /*
         * Use fallback merchant.
         * */
        if (!$invoice->hasMerchant() && $this->defaultMerchant === null) {
            throw new InvalidArgumentException("No merchant configured on invoice! Please specify the merchant on the invoice or setup the default merchant in the configuration!");
        }
        elseif (!$invoice->hasMerchant() && $this->defaultMerchant) {
            $invoice->setMerchant($this->defaultMerchant);
        }

        /*
         * Validate invoice for request
         * */
        $this->validateModel($invoice, $this->validationRulesForSavingInvoice());

        /*
         * Build invoice XML
         */
        $contents = $this->writer(
            function (XMLWriter $writer) use (&$invoice, &$withoutPdf, &$emailSubject, &$emailMessage) {

                /*
                 * Common settings of invoice
                 * */
                $writer->startElement('beallitasok');
                {
                    $this->writeCredentials($writer);
                    $writer->writeElement('eszamla', $this->stringifyBoolean($invoice->isElectronic));
                    $writer->writeElement('kulcstartojelszo', '');
                    $writer->writeElement('szamlaLetoltes', $this->stringifyBoolean(!$withoutPdf));
                    $writer->writeElement('valaszVerzio', 2);
                    $writer->writeElement('aggregator', '');
                }
                $writer->endElement();

                /*
                 * Header info of invoice
                 * */
                $writer->startElement('fejlec');
                {
                    $writer->writeElement('keltDatum', $invoice->createdAt->format('Y-m-d'));
                    $writer->writeElement('teljesitesDatum', $invoice->fulfillmentAt->format('Y-m-d'));
                    $writer->writeElement('fizetesiHataridoDatum', $invoice->paymentDeadline->format('Y-m-d'));
                    $writer->writeElement('fizmod', $this->getPaymentMethodByAlias($invoice->paymentMethod));
                    $writer->writeElement('penznem', $invoice->currency);
                    $writer->writeElement('szamlaNyelve', $invoice->invoiceLanguage);
                    $this->writeCdataElement($writer, 'megjegyzes', $invoice->comment ?: '');
                    if ($invoice->exchangeRateBank) {
                        $writer->writeElement('arfolyamBank', $invoice->exchangeRateBank);
                    }
                    if ($invoice->exchangeRate) {
                        $writer->writeElement('arfolyam', number_format($invoice->exchangeRate, 3, '.', ''));
                    }
                    if ($invoice->orderNumber) {
                        $this->writeCdataElement($writer, 'rendelesSzam', $invoice->orderNumber);
                    }
                    $writer->writeElement('elolegszamla', $this->stringifyBoolean($invoice->isImprestInvoice));
                    $writer->writeElement('vegszamla', $this->stringifyBoolean($invoice->isFinalInvoice));
                    $writer->writeElement('helyesbitoszamla', $this->stringifyBoolean($invoice->isReplacementInvoice));
                    $writer->writeElement('dijbekero', $this->stringifyBoolean(($invoice instanceof ProformaInvoice)));
                    if ($invoice->invoicePrefix) {
                        $this->writeCdataElement($writer, 'szamlaszamElotag', $invoice->invoicePrefix);
                    }
                    $writer->writeElement('fizetve', $this->stringifyBoolean($invoice->isPaid));
                }
                $writer->endElement();

                /*
                 * Merchant details
                 * */
                $writer->startElement('elado');
                {
                    $writer->writeElement('bank', $invoice->merchantBank);
                    $writer->writeElement('bankszamlaszam', $invoice->merchantBankAccountNumber);
                    if ($invoice->merchantReplyEmailAddress) {
                        $writer->writeElement('emailReplyto', $invoice->merchantReplyEmailAddress);
                    }
                    if ($emailSubject) {
                        $this->writeCdataElement($writer, 'emailTargy', $emailSubject);
                    }
                    if ($emailMessage) {
                        $this->writeCdataElement($writer, 'emailSzoveg', $emailMessage);
                    }
                }
                $writer->endElement();

                /*
                 * Customer details
                 * */
                $writer->startElement('vevo');
                {
                    $this->writeCdataElement($writer, 'nev', $invoice->customerName);
                    $this->writeCdataElement($writer, 'irsz', $invoice->customerZipCode);
                    $this->writeCdataElement($writer, 'telepules', $invoice->customerCity);
                    $this->writeCdataElement($writer, 'cim', $invoice->customerAddress);
                    if ($invoice->customerEmail) {
                        $writer->writeElement('email', $invoice->customerEmail);
                    }
                    $writer->writeElement('sendEmail', $this->stringifyBoolean($invoice->customerReceivesEmail));
                    if ($invoice->customerTaxNumber) {
                        $this->writeCdataElement($writer, 'adoszam', $invoice->customerTaxNumber);
                    }
                    if ($invoice->customerShippingName) {
                        $this->writeCdataElement($writer, 'postazasiNev', $invoice->customerShippingName);
                    }
                    if ($invoice->customerShippingZipCode) {
                        $this->writeCdataElement($writer, 'postazasiIrsz', $invoice->customerShippingZipCode);
                    }
                    if ($invoice->customerShippingCity) {
                        $this->writeCdataElement($writer, 'postazasiTelepules', $invoice->customerShippingCity);
                    }
                    if ($invoice->customerShippingAddress) {
                        $this->writeCdataElement($writer, 'postazasiCim', $invoice->customerShippingAddress);
                    }
                }
                $writer->endElement();

                /*
                 * Apply items
                 * */
                $writer->startElement('tetelek');
                $invoice->items()->each(function (array $item) use (&$writer) {
                    $writer->startElement('tetel');
                    {
                        $this->writeCdataElement($writer, 'megnevezes', $item['name']);
                        $writer->writeElement('mennyiseg', $item['quantity']);
                        $this->writeCdataElement($writer, 'mennyisegiEgyseg', $item['quantityUnit']);
                        $writer->writeElement('nettoEgysegar', $this->commonCurrencyFormat($item['netUnitPrice']));
                        $writer->writeElement('afakulcs', $item['taxRate']);

                        $netUnitPrice = $item['netUnitPrice'];
                        $taxRate      = is_numeric($item['taxRate']) ? $item['taxRate'] : 0;
                        $quantity     = $item['quantity'];
                        $netPrice     = isset($item['netPrice'])
                            ? $item['netPrice']
                            : ($netUnitPrice * $quantity);
                        $grossPrice   = isset($item['grossPrice'])
                            ? $item['grossPrice']
                            : $netPrice * (1 + ($taxRate / 100));
                        $taxValue     = isset($item['taxValue'])
                            ? $item['taxValue']
                            : ($grossPrice - $netPrice);

                        $writer->writeElement('nettoErtek', $this->commonCurrencyFormat($netPrice));
                        $writer->writeElement('afaErtek', $this->commonCurrencyFormat($taxValue));
                        $writer->writeElement('bruttoErtek', $this->commonCurrencyFormat($grossPrice));
                        if (isset($item['comment']) && !empty($item['comment'])) {
                            $this->writeCdataElement($writer, 'megjegyzes', $item['comment']);
                        }
                    }
                    $writer->endElement();
                });
                $writer->endElement();

            },
            ...self::ACTIONS['UPLOAD_COMMON_INVOICE']['schema']
        );

        /*
         * Send invoice
         * */
        $response = new InvoiceCreationResponse(
            $this,
            $this->send(self::ACTIONS['UPLOAD_COMMON_INVOICE']['name'], $contents)
        );

        // Assign invoice number on invoice
        $invoice->invoiceNumber = $response->invoiceNumber;

        /*
         * Saving (proforma) invoice PDF files - generated by the API
         * */
        if ($response->pdfBase64 &&
            !$withoutPdf &&
            $this->shouldSavePdf()
        ) {

            $disk = $this->storageDisk();
            $path = $this->storagePath();

            /*
             * Save generated invoice PDF file
             * */
            if ($response->pdfBase64 !== null) {
                $this->updatePdfFile(
                    $disk,
                    $path,
                    base64_decode($response->pdfBase64),
                    "$response->invoiceNumber.pdf"
                );
            }
        }

        return $response;

    }

    /**
     * Deletes only proforma invoices
     *
     * @param ProformaInvoice $invoice
     *
     * @return ProformaInvoiceDeletionResponse
     * @throws ModelValidationException
     */
    public function deleteProFormaInvoice(ProformaInvoice $invoice)
    {

        $this->validateModel($invoice, $this->validationRulesForDeletingProformaInvoice());

        $contents = $this->writer(
            function (XMLWriter $writer) use ($invoice) {

                /*
                 * Common settings of invoice
                 * */
                $writer->startElement('beallitasok');
                {
                    $this->writeCredentials($writer);
                }
                $writer->endElement();

                $writer->startElement('fejlec');
                {
                    $writer->writeElement('szamlaszam', $invoice->invoiceNumber);
                }
                $writer->endElement();

            },
            ...self::ACTIONS['DELETE_PROFORMA_INVOICE']['schema']);

        return new ProformaInvoiceDeletionResponse(
            $invoice,
            $this,
            $this->send(self::ACTIONS['DELETE_PROFORMA_INVOICE']['name'], $contents)
        );
    }

    /**
     * Cancels (existing) invoice
     *
     * @param Invoice $invoice
     * @param bool    $withoutPdf
     * @param null    $emailSubject
     * @param null    $emailMessage
     *
     * @return InvoiceCancellationResponse
     * @throws InvoiceValidationException
     * @throws ReceiptValidationException
     */
    public function cancelInvoice(Invoice $invoice, $withoutPdf = false, $emailSubject = null, $emailMessage = null)
    {

        /*
         * Validate invoice for request
         * */
        $this->validateModel($invoice, $this->validationRulesForCancellingInvoice());

        /*
         * Build invoice XML
         */
        $contents = $this->writer(
            function (XMLWriter $writer) use (&$invoice, &$emailSubject, &$emailMessage) {

                /*
                 * Common settings of invoice
                 * */
                $writer->startElement('beallitasok');
                {
                    $this->writeCredentials($writer);
                    $writer->writeElement('eszamla', $this->stringifyBoolean($invoice->isElectronic));
                    $writer->writeElement('kulcstartojelszo', '');
                    $writer->writeElement('szamlaLetoltes', $this->stringifyBoolean(false));
                    $writer->writeElement('szamlaLetoltesPld', 1);
                }
                $writer->endElement();

                $writer->startElement('fejlec');
                {
                    $writer->writeElement('szamlaszam', $invoice->invoiceNumber);
                    $writer->writeElement('keltDatum', $invoice->createdAt->format('Y-m-d'));
                    $writer->writeElement('teljesitesDatum', $invoice->fulfillmentAt->format('Y-m-d'));
                    $writer->writeElement('tipus', 'SS');
                }
                $writer->endElement();

                $writer->startElement('elado');
                {
                    if ($invoice->customerReceivesEmail) {
                        if ($invoice->merchantReplyEmailAddress) {
                            $writer->writeElement('emailReplyto', $invoice->merchantReplyEmailAddress);
                        }
                        if ($emailSubject) {
                            $this->writeCdataElement($writer, 'emailTargy', $emailSubject);
                        }
                        if ($emailMessage) {
                            $this->writeCdataElement($writer, 'emailSzoveg', $emailMessage);
                        }
                    }
                }
                $writer->endElement();

                $writer->startElement('vevo');
                {
                    $writer->writeElement('email', $invoice->customerEmail);
                }
                $writer->endElement();

            },
            ...self::ACTIONS['CANCEL_INVOICE']['schema']
        );

        $response = new InvoiceCancellationResponse(
            $invoice,
            $this,
            $this->send(self::ACTIONS['CANCEL_INVOICE']['name'], $contents));


        // Since the API responds with XML or PDF we have to choose one.
        if (!$withoutPdf && $this->shouldSavePdf()) {

            $contents = $this->writer(
                function (XMLWriter $writer) use (&$invoice) {

                    /*
                     * Common settings of invoice
                     * */
                    $writer->startElement('beallitasok');
                    {
                        $this->writeCredentials($writer);
                        $writer->writeElement('eszamla', $this->stringifyBoolean($invoice->isElectronic));
                        $writer->writeElement('kulcstartojelszo', '');
                        $writer->writeElement('szamlaLetoltes', $this->stringifyBoolean(true));
                        $writer->writeElement('szamlaLetoltesPld', 1);
                    }
                    $writer->endElement();

                    $writer->startElement('fejlec');
                    {
                        $writer->writeElement('szamlaszam', $invoice->invoiceNumber);
                    }
                    $writer->endElement();
                },
                ...self::ACTIONS['CANCEL_INVOICE']['schema']
            );

            $pdf = (string)$this->send(self::ACTIONS['CANCEL_INVOICE']['name'], $contents)->getBody();
            $this->updatePdfFile($this->storageDisk(), $this->storagePath(), $pdf, "$response->cancellationInvoiceNumber.pdf");
        }

        return $response;
    }

    /**
     * @param $orderNumber
     *
     * @return Invoice|ProformaInvoice|AbstractInvoice
     * @throws CommonResponseException
     */
    public function getInvoiceByOrderNumber($orderNumber)
    {
        try {
            return $this->getInvoiceByOrderNumberOrFail($orderNumber);
        }
        catch (InvoiceNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param $orderNumber
     *
     * @return mixed
     * @throws CommonResponseException
     * @throws InvoiceNotFoundException
     */
    public function getInvoiceByOrderNumberOrFail($orderNumber)
    {
        [$head, $customer, $merchant, $items] = $this->getCommonInvoice(null, $orderNumber);
        return $this->invoiceFactory(
            $head['isPrepaymentRequest'] ? ProformaInvoice::class : Invoice::class,
            $head, $customer, $merchant, $items
        );
    }

    /**
     * @param string|Invoice $invoice
     *
     * @return AbstractInvoice|Invoice|ProformaInvoice
     * @throws CommonResponseException
     * @throws InvoiceNotFoundException
     */
    public function getInvoiceOrFail($invoice)
    {
        if (!is_string($invoice) && !$invoice instanceof Invoice) {
            throw new InvalidArgumentException("Invoice needs to be either invoice number string or instance of [" . Invoice::class . "]");
        }

        return $this->invoiceFactory(Invoice::class, ...$this->getCommonInvoice($invoice instanceof Invoice ? $invoice->invoiceNumber : $invoice));
    }

    /**
     * @param string|Invoice $invoice
     *
     * @return null|AbstractInvoice|Invoice|ProformaInvoice
     * @throws CommonResponseException
     */
    public function getInvoice($invoice)
    {
        try {
            return $this->getInvoiceOrFail($invoice);
        }
        catch (InvoiceNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param string|ProformaInvoice $invoice
     *
     * @return AbstractInvoice|Invoice|ProformaInvoice
     * @throws CommonResponseException
     * @throws InvoiceNotFoundException
     * @throws InvalidArgumentException
     */
    public function getProformaInvoiceOrFail($invoice)
    {
        if (!is_string($invoice) && !$invoice instanceof ProformaInvoice) {
            throw new InvalidArgumentException("Invoice needs to be either invoice number string or instance of [" . ProformaInvoice::class . "]");
        }

        return $this->invoiceFactory(ProformaInvoice::class, ...$this->getCommonInvoice($invoice instanceof ProformaInvoice ? $invoice->invoiceNumber : $invoice));
    }

    /**
     * @param string|ProformaInvoice $invoice
     *
     * @return null|ProformaInvoice
     * @throws CommonResponseException
     */
    public function getProformaInvoice($invoice)
    {
        try {
            return $this->getProformaInvoiceOrFail($invoice);
        }
        catch (InvoiceNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param string|AbstractInvoice|null $invoiceNumber
     * @param null                        $orderNumber
     *
     * @return array
     * @throws CommonResponseException
     * @throws InvoiceNotFoundException
     * @throws InvalidArgumentException
     */
    protected function getCommonInvoice($invoiceNumber = null, $orderNumber = null)
    {

        if (!$invoiceNumber && !$orderNumber) {
            throw new InvalidArgumentException('Invoice or the orderNumber must be specified!');
        }

        /*
         * Build invoice XML
         */
        $contents = $this->writer(
            function (XMLWriter $writer) use (&$invoiceNumber, &$orderNumber) {
                $this->writeCredentials($writer);
                if ($orderNumber) {
                    $writer->writeElement('rendelesSzam', $orderNumber);
                }
                else {
                    $writer->writeElement('szamlaszam', $invoiceNumber);
                }
            },
            ...self::ACTIONS['GET_COMMON_INVOICE']['schema']
        );

        try {

            /*
             * Response obtained
             * */
            $contents = (string)$this->send(self::ACTIONS['GET_COMMON_INVOICE']['name'], $contents)->getBody();

            $xml = $this->parse($contents);


            // General attributes
            $head = [
                'isElectronic'        => Str::startsWith($xml['alap']['szamlaszam'], 'E-'),
                'isPrepaymentRequest' => Str::startsWith($xml['alap']['szamlaszam'], 'D-'),
                'invoiceNumber'       => $xml['alap']['szamlaszam'],
                'createdAt'           => Carbon::createFromFormat('Y-m-d', $xml['alap']['kelt']),
                'fulfillmentAt'       => Carbon::createFromFormat('Y-m-d', $xml['alap']['telj']),
                'paymentDeadline'     => Carbon::createFromFormat('Y-m-d', $xml['alap']['fizh']),
                'paymentMethod'       => $this->getPaymentMethod(html_entity_decode($xml['alap']['fizmod'])),
                'invoiceLanguage'     => $xml['alap']['nyelv'],
                'currency'            => $xml['alap']['devizanem'],
                'exchangeRateBank'    => isset($xml['alap']['devizabank']) ? $xml['alap']['devizabank'] : null,
                'exchangeRate'        => isset($xml['alap']['devizaarf']) ? $xml['alap']['devizaarf'] : null,
                'comment'             => html_entity_decode($xml['alap']['megjegyzes']),
                'isKata'              => $xml['alap']['kata'] == 'true',
            ];

            if (isset($xml['alap']['hivdijbekszam'])) {
                $head['proFormaInvoiceNumber'] = $xml['alap']['hivdijbekszam'];
            }

            if (isset($xml['alap']['rendelesszam'])) {
                $head['orderNumber'] = $xml['alap']['rendelesszam'];
            }

            // Customer fields
            $customer = [
                'customerName'      => html_entity_decode($xml['vevo']['nev']),
                'customerCountry'   => html_entity_decode($xml['vevo']['cim']['orszag']),
                'customerZipCode'   => $xml['vevo']['cim']['irsz'],
                'customerCity'      => html_entity_decode($xml['vevo']['cim']['telepules']),
                'customerAddress'   => $xml['vevo']['cim']['cim'],
                'customerTaxNumber' => $xml['vevo']['adoszam'],
            ];

            // Merchant fields
            $merchant = [
                'merchantName'              => html_entity_decode($xml['szallito']['nev']),
                'merchantCountry'           => html_entity_decode($xml['szallito']['cim']['orszag']),
                'merchantZipCode'           => html_entity_decode($xml['szallito']['cim']['irsz']),
                'merchantCity'              => html_entity_decode($xml['szallito']['cim']['telepules']),
                'merchantAddress'           => html_entity_decode($xml['szallito']['cim']['cim']),
                'merchantTaxNumber'         => html_entity_decode($xml['szallito']['adoszam']),
                'merchantEuTaxNumber'       => isset($xml['szallito']['adoszameu']) ? html_entity_decode($xml['szallito']['adoszameu']) : null,
                'merchantBank'              => html_entity_decode($xml['szallito']['bank']['nev']),
                'merchantBankAccountNumber' => $xml['szallito']['bank']['bankszamla'],
            ];

            // Items
            $items = Collection::wrap($items = $this->normalizeToNumericArray($xml['tetelek']['tetel']))
                ->map(function ($item) {
                    return [
                        'name'            => html_entity_decode($item['nev']),
                        'quantity'        => (double)$item['mennyiseg'],
                        'quantityUnit'    => $item['mennyisegiegyseg'],
                        'netUnitPrice'    => (double)$item['nettoegysegar'],
                        'taxRate'         => is_numeric($item['afakulcs']) ? (double)$item['afakulcs'] : $item['afakulcs'],
                        'totalNetPrice'   => (double)$item['netto'],
                        'taxValue'        => (double)$item['afa'],
                        'totalGrossPrice' => (double)$item['brutto'],
                        'comment'         => html_entity_decode($item['megjegyzes']),
                    ];
                })
                ->toArray();

        }
        catch (CommonResponseException $exception) {

            if (Str::contains((string)$exception->getResponse()->getBody(), '(ismeretlen számlaszám).')) {
                throw new InvoiceNotFoundException($invoiceNumber);
            }

            throw $exception;
        }
        catch (ParserException $exception) {
            throw new InvoiceNotFoundException($invoiceNumber);
        }

        return [
            $head,
            $customer,
            $merchant,
            $items,
        ];
    }

    /**
     * @param Receipt $receipt
     * @param bool    $withoutPdf
     *
     * @return ReceiptCreationResponse
     * @throws ModelValidationException
     */
    public function uploadReceipt(Receipt $receipt, $withoutPdf = false)
    {

        /*
         * Validate receipt for request
         * */
        $this->validateModel($receipt, $this->validationRulesForSavingReceipt());

        $contents = $this->writer(function (XMLWriter $writer) use (&$receipt, &$withoutPdf) {

            $writer->startElement('beallitasok');
            {
                $this->writeCredentials($writer);
                $writer->writeElement('pdfLetoltes', $this->stringifyBoolean(!$withoutPdf || $this->shouldSavePdf()));
            }
            $writer->endElement();

            /*
             * Header info of receipt
             * */
            $writer->startElement('fejlec');
            {
                $writer->writeElement('hivasAzonosito', $receipt->orderNumber);
                $writer->writeElement('elotag', $receipt->prefix);
                $writer->writeElement('fizmod', $this->getPaymentMethodByAlias($receipt->paymentMethod));
                $writer->writeElement('penznem', $receipt->currency);
                if ($receipt->exchangeRateBank) {
                    $writer->writeElement('devizabank', $receipt->exchangeRateBank);
                }
                if ($receipt->exchangeRate) {
                    $writer->writeElement('devizaarf', $receipt->exchangeRate);
                }
                $writer->writeElement('megjegyzes', $receipt->comment);
            }
            $writer->endElement();

            /*
             * Writing items
             * */
            $writer->startElement('tetelek');
            $receipt->items()->each(function (array $item) use (&$writer) {
                $writer->startElement('tetel');
                {
                    $this->writeCdataElement($writer, 'megnevezes', $item['name']);
                    $writer->writeElement('mennyiseg', $item['quantity']);
                    $this->writeCdataElement($writer, 'mennyisegiEgyseg', $item['quantityUnit']);
                    $writer->writeElement('nettoEgysegar', $this->commonCurrencyFormat($item['netUnitPrice']));
                    $writer->writeElement('afakulcs', $item['taxRate']);

                    $netUnitPrice = $item['netUnitPrice'];
                    $taxRate      = is_numeric($item['taxRate']) ? $item['taxRate'] : 0;
                    $quantity     = $item['quantity'];
                    $netPrice     = isset($item['netPrice']) ? $item['netPrice'] : ($netUnitPrice * $quantity);
                    $grossPrice   = isset($item['grossPrice']) ? $item['grossPrice'] : $netPrice * (1 + ($taxRate / 100));
                    $taxValue     = isset($item['taxValue']) ? $item['taxValue'] : ($grossPrice - $netPrice);

                    $writer->writeElement('netto', $this->commonCurrencyFormat($netPrice));
                    $writer->writeElement('afa', $this->commonCurrencyFormat($taxValue));
                    $writer->writeElement('brutto', $this->commonCurrencyFormat($grossPrice));

                }
                $writer->endElement();
            });
            $writer->endElement();

            /*
             * Writing payments if present
             * */
            if ($receipt->payments()->isNotEmpty()) {
                $writer->startElement('kifizetesek');
                $receipt->payments()->each(function ($payment) use (&$writer) {
                    $writer->startElement('kifizetes');
                    {
                        $writer->writeElement('fizetoeszkoz', $this->getPaymentMethodByAlias($payment['paymentMethod']));
                        $writer->writeElement('osszeg', $this->commonCurrencyFormat($payment['amount']));
                        if (isset($payment['comment']) && !empty($payment['comment'])) {
                            $this->writeCdataElement($writer, 'leiras', $payment['comment']);
                        }
                    }
                    $writer->endElement();
                });
                $writer->endElement();
            }

        },
            ...self::ACTIONS['UPLOAD_RECEIPT']['schema']
        );

        $response = new ReceiptCreationResponse(
            $this,
            $this->send(self::ACTIONS['UPLOAD_RECEIPT']['name'], $contents)
        );

        // Fill up related attributes
        $receipt->fill([
            'callId'        => $response->callId,
            'receiptNumber' => $response->receiptNumber,
            'createdAt'     => $response->createdAt,
            'isCancelled'   => $response->isCancelled,
        ]);

        /*
         * Saving receipt PDF files - generated by remote API
         * */
        if ($this->shouldSavePdf() && !$withoutPdf) {

            $this->updatePdfFile(
                $this->storageDisk(),
                $this->storagePath(),
                base64_decode($response->pdfBase64),
                "$response->receiptNumber.pdf"
            );
        }

        return $response;
    }

    /**
     * @param Receipt $receipt
     * @param bool    $withoutPdf
     *
     * @return ReceiptCancellationResponse
     * @throws ModelValidationException
     */
    public function cancelReceipt(Receipt $receipt, $withoutPdf = false)
    {

        $this->validateModel($receipt, $this->validationRulesForCancellingReceipt());

        $contents = $this->writer(function (XMLWriter $writer) use (&$receipt, &$withoutPdf) {

            $writer->startElement('beallitasok');
            {
                $this->writeCredentials($writer);
                $writer->writeElement('pdfLetoltes', $this->stringifyBoolean(!$withoutPdf || $this->shouldSavePdf()));
            }
            $writer->endElement();

            $writer->startElement('fejlec');
            {
                $writer->writeElement('nyugtaszam', $receipt->receiptNumber);
            }
            $writer->endElement();

        },
            ...self::ACTIONS['CANCEL_RECEIPT']['schema']);

        $response = new ReceiptCancellationResponse(
            $receipt,
            $this,
            $this->send(self::ACTIONS['CANCEL_RECEIPT']['name'], $contents)
        );

        if ($response->pdfBase64 && $this->shouldSavePdf() && !$withoutPdf) {

            $this->updatePdfFile(
                $this->storageDisk(),
                $this->storagePath(),
                base64_decode($response->pdfBase64),
                $response->originalReceiptNumber . ".pdf"
            );
        }

        // Modify related attributes
        $receipt->fill([
            'isCancelled' => true,
        ]);

        return $response;

    }

    /**
     * @param Receipt $receipt
     * @param bool    $withoutPdf
     *
     * @return null|Receipt
     * @throws ModelValidationException
     */
    public function getReceipt(Receipt $receipt, $withoutPdf = false)
    {
        try {
            return $this->getReceiptOrFail($receipt, $withoutPdf);
        }
        catch (ReceiptNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param Receipt $receipt
     * @param bool    $withoutPdf
     *
     * @return Receipt
     * @throws ModelValidationException
     * @throws ReceiptNotFoundException
     */
    public function getReceiptOrFail(Receipt $receipt, $withoutPdf = false)
    {

        $this->validateModel($receipt, $this->validationRulesForObtainingReceipt());

        return $this->getReceiptByReceiptNumberOrFail($receipt->receiptNumber, $withoutPdf);
    }

    /**
     * @param      $receiptNumber
     * @param bool $withoutPdf
     *
     * @return Receipt|null
     */
    public function getReceiptByReceiptNumber($receiptNumber, $withoutPdf = false)
    {
        try {
            return $this->getReceiptByReceiptNumberOrFail($receiptNumber, $withoutPdf);
        }
        catch (ReceiptNotFoundException $exception) {
            return null;
        }
    }

    /**
     * @param string $receiptNumber
     * @param bool   $withoutPdf
     *
     * @return Receipt
     * @throws ReceiptNotFoundException
     */
    public function getReceiptByReceiptNumberOrFail($receiptNumber, $withoutPdf = false)
    {

        $contents = $this->writer(function (XMLWriter $writer) use (&$receiptNumber, &$withoutPdf) {

            $writer->startElement('beallitasok');
            {
                $this->writeCredentials($writer);
                $writer->writeElement('pdfLetoltes', $this->stringifyBoolean(!$withoutPdf));
            }
            $writer->endElement();

            $writer->startElement('fejlec');
            {
                $writer->writeElement('nyugtaszam', $receiptNumber);
            }
            $writer->endElement();

        },
            ...self::ACTIONS['GET_RECEIPT']['schema']);

        $contents = (string)$this->send(self::ACTIONS['GET_RECEIPT']['name'], $contents)->getBody();

        try {

            $xml = $this->parse($contents);

            // General attributes
            $head = [
                'callId'                => isset($xml['nyugta']['alap']['hivasAzonosito']) ? $xml['nyugta']['alap']['hivasAzonosito'] : null,
                'receiptNumber'         => $xml['nyugta']['alap']['nyugtaszam'],
                'isCancelled'           => $xml['nyugta']['alap']['stornozott'] === 'true',
                'createdAt'             => Carbon::createFromFormat('Y-m-d', $xml['nyugta']['alap']['kelt']),
                'exchangeRateBank'      => isset($xml['nyugta']['alap']['devizabank'])
                    ? $xml['nyugta']['alap']['devizabank']
                    : null,
                'exchangeRate'          => isset($xml['nyugta']['alap']['devizaarf'])
                    ? (double)$xml['nyugta']['alap']['devizaarf']
                    : null,
                'paymentMethod'         => $this->getPaymentMethodByType(html_entity_decode($xml['nyugta']['alap']['fizmod'])),
                'currency'              => $xml['nyugta']['alap']['penznem'],
                'comment'               => isset($xml['nyugta']['alap']['megjegyzes']) ? $xml['nyugta']['alap']['megjegyzes'] : null,
                'originalReceiptNumber' => isset($xml['nyugta']['alap']['stornozottNyugtaszam'])
                    ? $xml['nyugta']['alap']['stornozottNyugtaszam']
                    : null,
            ];

            // Items
            $items = [];
            if (isset($xml['nyugta']['tetelek']) && isset($xml['nyugta']['tetelek']['tetel'])) {
                $items = Collection::wrap($this->normalizeToNumericArray($xml['nyugta']['tetelek']['tetel']))
                    ->map(function ($item) {
                        return [
                            'name'            => $item['megnevezes'],
                            'quantity'        => (double)$item['mennyiseg'],
                            'quantityUnit'    => $item['mennyisegiEgyseg'],
                            'netUnitPrice'    => (double)$item['nettoEgysegar'],
                            'totalNetPrice'   => (double)$item['netto'],
                            'taxRate'         => is_numeric($item['afakulcs']) ? (double)$item['afakulcs'] : $item['afakulcs'],
                            'taxValue'        => (double)$item['afa'],
                            'totalGrossPrice' => (double)$item['brutto'],
                        ];
                    })
                    ->toArray();
            }

            // Payments
            $payments = [];
            if (isset($xml['nyugta']['kifizetesek']) && isset($xml['nyugta']['kifizetesek']['kifizetes'])) {
                $payments = Collection::wrap($this->normalizeToNumericArray($xml['nyugta']['kifizetesek']['kifizetes']))
                    ->map(function ($payment) {
                        return [
                            'paymentMethod' => $this->getPaymentMethodByType(html_entity_decode($payment['fizetoeszkoz'])),
                            'amount'        => (float)$payment['osszeg'],
                            'comment'       => isset($payment['leiras']) ? $payment['leiras'] : null,
                        ];
                    })
                    ->toArray();
            }

            /*
             * Saving receipt PDF files - generated by remote API
             * */
            if (isset($xml['nyugtaPdf']) && $xml['nyugtaPdf'] !== '' && $this->shouldSavePdf() && !$withoutPdf) {

                $this->updatePdfFile(
                    $this->storageDisk(),
                    $this->storagePath(),
                    base64_decode($xml['nyugtaPdf']),
                    $xml['nyugta']['alap']['nyugtaszam'] . ".pdf"
                );
            }

        }
        catch (ParserException $exception) {
            throw new ReceiptNotFoundException($receiptNumber);
        }

        return new Receipt($head, $items, $payments);
    }

}
