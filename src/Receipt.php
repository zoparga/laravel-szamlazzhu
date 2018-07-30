<?php


namespace SzuniSoft\SzamlazzHu;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use SzuniSoft\SzamlazzHu\Client\Errors\CancellationReceiptObtainException;
use SzuniSoft\SzamlazzHu\Client\Errors\ModelValidationException;
use SzuniSoft\SzamlazzHu\Client\Errors\ReceiptAlreadyCancelledException;
use SzuniSoft\SzamlazzHu\Client\Errors\ReceiptValidationException;
use SzuniSoft\SzamlazzHu\Client\Models\ReceiptCancellationResponse;
use SzuniSoft\SzamlazzHu\Client\Models\ReceiptCreationResponse;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItem;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItemCollection;
use SzuniSoft\SzamlazzHu\Contracts\ArrayablePayment;
use SzuniSoft\SzamlazzHu\Contracts\ArrayablePaymentCollection;
use SzuniSoft\SzamlazzHu\Internal\AbstractModel;
use SzuniSoft\SzamlazzHu\Internal\Support\ClientAccessor;
use SzuniSoft\SzamlazzHu\Internal\Support\ItemHolder;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentHolder;

/**
 * Class Receipt
 * @package SzuniSoft\SzamlazzHu
 *
 * [Attributes]
 * @property string $prefix
 * @property string $receiptNumber
 *
 * [Read only attributes]
 * @property-read string $callId
 * @property-read Carbon $createdAt
 * @property-read boolean $isCancelled
 * @property-read string $cancellationReceiptNumber
 * @property-read string $originalReceiptNumber
 */
class Receipt extends AbstractModel {

    use ClientAccessor,
        ItemHolder,
        PaymentHolder {

        ItemHolder::isEmpty insteadof PaymentHolder;
        ItemHolder::isEmpty as doesNotHaveItems;
        PaymentHolder::isEmpty as doesNotHavePayments;

        ItemHolder::total insteadof PaymentHolder;
        ItemHolder::total as totalItems;
        PaymentHolder::total as totalPayments;
    }

    /**
     * @var self
     */
    protected $cancellationReceipt = null;

    /**
     * @var self
     */
    protected $originalReceipt = null;

    /**
     * @var array
     */
    protected $attributes = [
        'currency' => 'EUR',
    ];

    /**
     * Receipt constructor.
     * @param array|Receipt|null $attributes
     * @param array|ArrayableItem[]|ArrayableItemCollection|Collection|null $items
     * @param array|ArrayablePayment[]|ArrayablePaymentCollection|Collection|null $payments
     */
    public function __construct($attributes = null, $items = null, $payments = null)
    {

        if ($attributes) {

            if ($attributes instanceof Receipt) {
                $ancestorItems = $attributes->items();
                $ancestorPayments = $attributes->payments();
                $attributes = $attributes->attributes;

                if ($this->items->isEmpty() && !$ancestorItems->isEmpty()) {
                    $this->items = $ancestorItems;
                }

                if ($this->payments->isEmpty() && !$ancestorPayments->isEmpty()) {
                    $this->payments = $ancestorPayments;
                }
            }

            $this->fill($attributes);
        }

        if (!$this->items && !$items) {
            $items = [];
        }

        if (!$this->payments && !$payments) {
            $payments = [];
        }

        $this->items = Collection::wrap($items);
        $this->payments = Collection::wrap($payments);
    }


    /**
     * @return array
     */
    protected function apiAttributes()
    {
        return [
            'orderNumber',
            'prefix',
            'currency',
            'comment',
            'paymentMethod',
            'exchangeRateBank',
            'exchangeRate',
            'receiptNumber'
        ];
    }

    /**
     * The API manifest of model
     * @return array
     */
    function toApiArray()
    {
        return array_merge(
            parent::toApiArray(),
            [
                'items' => $this->itemsToArray(),
                'payments' => $this->paymentsToArray()
            ]
        );
    }

    /**
     * Retrieves the original receipt which was cancelled.
     * In this case the current instance is a cancellation receipt.
     *
     * @return self|null
     * @throws CancellationReceiptObtainException
     */
    public function getCancellationReceipt()
    {
        if ($this->isCancelled && $this->cancellationReceiptNumber && $this->cancellationReceipt === null) {
            return $this->cancellationReceipt = $this->getClient()->getReceiptByReceiptNumber($this->cancellationReceiptNumber);
        }
        else if (!$this->cancellationReceiptNumber) {
            throw new CancellationReceiptObtainException();
        }
        return $this->cancellationReceipt;
    }

    /**
     *
     */
    public function getOriginalReceipt()
    {
        if ($this->isCancelled && $this->originalReceiptNumber) {
            return $this->originalReceipt = $this->getClient()->getReceiptByReceiptNumber($this->originalReceiptNumber);
        }
        return $this->originalReceipt;
    }

    /**
     * @return bool
     */
    public function hasOriginalReceipt()
    {
        return $this->isCancelled && !!$this->originalReceiptNumber;
    }

    /**
     * @return $this
     * @throws ReceiptValidationException|ModelValidationException
     */
    public function validateForSave()
    {
        $this->getClient()->validateReceiptForSaving($this);
        return $this;
    }

    /**
     * @param bool $withoutPdf
     * @return Receipt
     * @throws Client\Errors\ModelValidationException
     */
    public function update($withoutPdf = false)
    {

        $alias = $this->getClient()->getReceipt($this, $withoutPdf);

        if ($alias) {
            $this->fill($alias->attributes);
            $this->items = $alias->items;
            $this->payments = $alias->payments;
        }

        return $this;
    }

    /**
     * @param bool $withoutPdf
     * @param ReceiptCreationResponse $response
     * @return Receipt
     * @throws ModelValidationException
     */
    public function save($withoutPdf = false, ReceiptCreationResponse &$response = null)
    {
        $response = $this->getClient()->uploadReceipt($this, $withoutPdf);
        return $this;
    }

    /**
     * @param bool $withoutPdf
     * @param ReceiptCancellationResponse $response
     * @return Receipt
     * @throws ModelValidationException
     * @throws ReceiptAlreadyCancelledException
     */
    public function cancel($withoutPdf = false, ReceiptCancellationResponse &$response = null)
    {
        if ($this->isCancelled) {
            throw new ReceiptAlreadyCancelledException($this);
        }

        $response = $this->getClient()->cancelReceipt($this, $withoutPdf);
        $this->cancellationReceiptNumber = $response->newReceiptNumber;
        return $this;
    }

}