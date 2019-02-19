<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;
use SzuniSoft\SzamlazzHu\Contracts\ArrayableMerchant;

/**
 * Trait SimplifiesMerchant
 * @package SzuniSoft\SzamlazzHu\Support
 *
 * [Attributes]
 * @property-read string $merchantBank
 * @property-read string $merchantBankAccountNumber
 * @property-read string $merchantReplyEmailAddress
 * @property-read string $merchantSignature
 *
 * [Readonly attributes]
 * @property-read string $merchantName
 * @property-read string $merchantCountry
 * @property-read string $merchantZipCode
 * @property-read string $merchantCity
 * @property-read string $merchantAddress
 * @property-read string $merchantTaxNumber
 * @property-read string $merchantEuTaxNumber
 */
trait MerchantHolder
{

    /**
     * @var array|null
     */
    protected $merchant = null;

    /**
     * @var array
     */
    protected $merchantAttributes = [
        'merchantBank',
        'merchantBankAccountNumber',
        'merchantReplyEmailAddress',
        'merchantSignature',
    ];

    /**
     * @param array|ArrayableMerchant $merchant
     * @return array
     */
    protected function simplifyMerchant($merchant)
    {
        if (! is_array($merchant) && ! $merchant instanceof ArrayableMerchant) {
            throw new InvalidArgumentException("Specified merchant must be an array or must implement [" . class_basename(ArrayableMerchant::class) . "]");
        }
        return ($merchant instanceof ArrayableMerchant) ? $merchant->toMerchantArray() : (array) $merchant;
    }

    /**
     * Sets merchant details on invoice
     *
     * @param array|ArrayableMerchant $merchant
     */
    public function setMerchant($merchant)
    {
        $merchant = $this->simplifyMerchant($merchant);
        foreach ($merchant as $key => $value) {
            $key = ! Str::startsWith($key, 'merchant') ? 'merchant' . ucfirst(Str::camel($key)) : lcfirst($key);
            $this->attributes[$key] = $value;
        }
        $this->merchant = $merchant;
    }

    /**
     * @return array|null
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return bool
     */
    public function hasMerchant()
    {
        return $this->merchant !== null;
    }

}