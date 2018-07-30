<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;


use Illuminate\Support\Collection;
use InvalidArgumentException;

trait PaymentMethods {

    /**
     * All the accepted payment methods
     *
     * @var array
     */
    public static $paymentMethods = [
        'transfer' => ['transfer', 'átutalás'],
        'cash' => ['cash', 'készpénz'],
        'bank_card' => ['bank_card', 'bankkártya'],
        'credit_card' => ['credit_card', 'bankkártya'],
        'check' => ['check', 'csekk'],
        'c.o.d.' => ['c.o.d.', 'utánvét'],
        'gift_card' => ['gift_card', 'ajándékutalvány'],
        'barter' => ['barter', 'barter'],
        'Borgun' => ['borgun', 'Borgun'],
        'group' => ['group', 'csoportos beszedés'],
        'EP_card' => ['ep_card', 'EP kártya'],
        'OTP_simple' => ['otp_simple', 'OTP Simple'],
        'compensation' => ['compensation', 'kompenzáció'],
        'coupon' => ['coupon', 'kupon'],
        'PayPal' => ['paypal', 'PayPal'],
        'PayU' => ['payu', 'PayU'],
        'SZÉP_card' => ['szép_card', 'SZÉP kártya'],
        'free_of_charge' => ['free_of_charge', 'térítésmentes'],
        'voucher' => ['voucher', 'utalvány']
    ];

    /**
     * All the accepted payment methods
     * @return array
     */
    protected function paymentMethods()
    {
        return Collection::wrap(static::$paymentMethods)->map(function ($method) {
            return $method[0];
        })->toArray();
    }

    /**
     * @param $type
     * @return mixed
     */
    protected function getPaymentMethodByType($type)
    {

        $method = Collection::wrap(static::$paymentMethods)->first(function ($method) use (&$type) {
            return mb_strtolower($method[1]) === mb_strtolower(trim($type));
        });

        if (!$method) {
            throw new InvalidArgumentException("Given payment method [$type] is unknown!");
        }

        return $method;
    }

    /**
     * @param $alias
     * @return mixed
     */
    protected function getPaymentMethodByAlias($alias)
    {
        $method = Collection::wrap(static::$paymentMethods)->first(function ($method) use (&$alias) {
            return $method[0] === $alias;
        });

        if (!$method) {
            throw new InvalidArgumentException("Given payment method [$alias] is unknown!");
        }

        return $method[1];
    }

    /**
     * @param $method
     * @return mixed
     */
    protected function getPaymentMethod($method)
    {
        try {
            return $this->getPaymentMethodByType($method);
        } catch (InvalidArgumentException $exception) {
            return $this->getPaymentMethodByType($this->getPaymentMethodByAlias($method));
        }
    }

    /**
     * @param array|string $method
     */
    protected function setPaymentMethodAttribute($method)
    {

        if (is_array($method) && count($method) > 1) {
            $method = $method[0];
        }

        $this->getPaymentMethodByAlias($method);
        $this->attributes['paymentMethod'] = $method;
    }

}