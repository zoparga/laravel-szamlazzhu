<?php


namespace SzuniSoft\SzamlazzHu\Internal;


use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableCustomer;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItem;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableItemCollection;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableMerchant;
use SzuniSoft\SzamlazzHu\Internal\Support\CustomerHolder;
use SzuniSoft\SzamlazzHu\Internal\Support\ItemHolder;
use SzuniSoft\SzamlazzHu\Internal\Support\MerchantHolder;

/**
 * Class AbstractInvoice
 * @package SzuniSoft\SzamlazzHu\Internal
 *
 * [Self attributes]
 * @property boolean $isElectronic
 * @property Carbon|string $createdAt
 * @property Carbon|string|null $fulfillmentAt
 * @property Carbon|string $paymentDeadline
 * @property string $invoiceLanguage (de, en, it, hu, fr, ro, sk ,hr)
 * @property boolean $isImprestInvoice
 * @property boolean $isFinalInvoice
 * @property boolean $isReplacementInvoice
 * @property boolean $isPaid
 * @property string $invoicePrefix
 * @property string $invoiceNumber
 * @property boolean $isKata
 * @property-read boolean $shouldBePaid
 *
 * [Readonly customer attributes]
 * @property-read string $customerCountry
 */
abstract class AbstractInvoice extends AbstractModel
{

    use MerchantHolder;
    use CustomerHolder;
    use ItemHolder;

    /**
     * All the supported languages
     *
     * @var array
     */
    public static $supportedLanguages = [
        'de', 'en', 'it', 'hu', 'fr', 'ro', 'sk', 'hr'
    ];

    /**
     * @return array
     */
    protected function apiAttributes()
    {
        return array_merge([
            'isElectronic',
            'createdAt',
            'fulfillmentAt',
            'paymentDeadline',
            'paymentMethod',
            'currency',
            'invoiceLanguage',
            'comment',
            'exchangeRateBank',
            'exchangeRate',
            'orderNumber',
            'isImprestInvoice',
            'isFinalInvoice',
            'isReplacementInvoice',
            'isPrepaymentRequest',
            'isPaid',
            'invoicePrefix',
            'invoiceNumber',
        ],
            $this->customerAttributes,
            $this->merchantAttributes);
    }

    /**
     * Default attributes
     * Important!
     * All the pre-defined attributes are going to be created as
     * XML tags even with an empty value. Do not add further ones.
     *
     * @var array
     */
    protected $attributes = [
        'isElectronic' => true,
        'comment' => '',
        'invoiceLanguage' => 'en',
        'currency' => 'EUR',
        'createdAt' => null,
        'paymentDeadline' => null,
        'isImprestInvoice' => false,
        'isFinalInvoice' => false,
        'isReplacementInvoice' => false,
        'isPrepaymentRequest' => false,
        'isPaid' => false,
        'fulfillmentAt' => null,
    ];

    /**
     * Invoice constructor.
     * @param array|AbstractInvoice $attributes
     * @param array|ArrayableItem[]|ArrayableItemCollection|Collection|null $items
     * @param array|ArrayableCustomer|null $customer
     * @param array|ArrayableMerchant|null $merchant
     */
    public function __construct($attributes = null, $items = null, $customer = null, $merchant = null)
    {

        $this->items = Collection::wrap($items ?: [])
            ->map(function ($item) {
                if ($item instanceof ArrayableItem) {
                    return $item->toItemArray();
                }
                return $item;
            });

        if ($attributes) {

            if ($attributes instanceof AbstractInvoice) {
                $ancestorItems = $attributes->items();
                $attributes = $attributes->attributes;
                if ($this->items->isEmpty() && ! $ancestorItems->isEmpty()) {
                    $this->items = $ancestorItems;
                }
            }

            // Retrieve only head attributes
            $headAttributes = Collection::wrap($attributes)
                ->filter(function ($value, $key) {
                    return ! Str::startsWith($key, ['merchant', 'customer']);
                })
                ->toArray();


            // All attributes prefixed with `customer`
            $customerAttributes = Collection::wrap($attributes)
                ->filter(function ($value, $key) {
                    return Str::startsWith($key, 'customer');
                })
                ->toArray();

            // All attributes prefixed with `merchant`
            $merchantAttributes = Collection::wrap($attributes)
                ->filter(function ($value, $key) {
                    return Str::startsWith($key, 'merchant');
                })
                ->toArray();

            // Fill up invoice head attributes
            if (! empty(! empty($headAttributes))) {
                $this->fill($headAttributes);
            }

        }

        $customerAttributes = (! $customer && isset($customerAttributes)) ? $customerAttributes : [];
        $merchantAttributes = (! $merchant && isset($merchantAttributes)) ? $merchantAttributes : [];

        // Set customer attributes
        if ($customer || ! empty($customerAttributes)) {
            $this->setCustomer($customer ?: $customerAttributes);
        }

        // Set merchant attributes
        if ($merchant || ! empty($merchantAttributes)) {
            $this->setMerchant($merchant ?: $merchantAttributes);
        }

        if (! $this->createdAt) {
            $this->createdAt = Carbon::now();
        }
    }

    /**
     * @param $lang
     */
    protected function setInvoiceLanguageAttribute($lang)
    {
        $lang = strtolower(trim($lang));
        if (! in_array($lang, static::$supportedLanguages)) {
            $list = implode(',', static::$supportedLanguages);
            throw new InvalidArgumentException("Invalid language [$lang] provided! Accepted languages are: [$list]");
        }
        $this->attributes['invoiceLanguage'] = $lang;
    }

    /**
     * @return bool
     */
    protected function getShouldBePaidAttribute()
    {
        return $this->paymentDeadline->isPast();
    }

    /**
     * Thee API manifest of model
     * @return array
     */
    public function toApiArray()
    {
        return array_merge(
            parent::toApiArray(),
            ['items' => $this->itemsToArray()]
        );
    }


}