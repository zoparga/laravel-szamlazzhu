<?php


namespace SzuniSoft\SzamlazzHu\Internal;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SzuniSoft\SzamlazzHu\Internal\Support\PaymentMethods;


/**
 * Class AbstractModel
 * @package SzuniSoft\SzamlazzHu\Internal
 *
 * [Ancestor attributes]
 * @property string $paymentMethod (transfer, cash, bank_card, check, etc..)
 * @property string $orderNumber
 * @property string $currency
 * @property string $exchangeRateBank
 * @property float $exchangeRate
 * @property string $comment
 */
abstract class AbstractModel
{

    use PaymentMethods;

    /**
     * Default attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @return array
     */
    abstract protected function apiAttributes();

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * The API manifest of model
     *
     * @return array
     */
    function toApiArray()
    {
        return Collection::wrap($this->attributes)
            ->filter(function ($value, $key) {
                return $this->isFieldForApi($key);
            })
            ->toArray();
    }

    /**
     * @param $name
     * @return bool
     */
    protected function isFieldForApi($name)
    {
        return in_array($name, $this->apiAttributes());
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (! property_exists($this, $name)) {


            $setterMethodName = 'set' . ucfirst(Str::camel($name)) . 'Attribute';
            if (method_exists($this, $setterMethodName)) {
                $this->{$setterMethodName}($value);
            }
            else {
                $this->attributes[$name] = $value;
            }
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {

        $getterMethodName = 'get' . ucfirst(Str::camel($name)) . 'Attribute';
        if (method_exists($this, $getterMethodName)) {
            return $this->{$getterMethodName}();
        }

        if (! isset($this->attributes[$name])) {
            return null;
        }

        return $this->attributes[$name];
    }

    /**
     * Just make sure you know what you're doing here
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param array $attributes
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * @return array
     */
    protected function getPaymentMethodsAttribute()
    {
        return $this->paymentMethods();
    }

}