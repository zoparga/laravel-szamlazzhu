<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;


use Illuminate\Support\Collection;
use InvalidArgumentException;
use SzuniSoft\SzamlazzHu\Contracts\ArrayablePayment;
use SzuniSoft\SzamlazzHu\Contracts\ArrayablePaymentCollection;

trait PaymentHolder {

    /**
     * @var Collection
     */
    protected $payments;

    /**
     * @param $payment
     * @return array
     */
    protected function simplifyPayment($payment)
    {
        if (!is_array($payment) && !$payment instanceof ArrayablePayment) {
            throw new InvalidArgumentException("Specified payment must be an array or must implement [" . get_class(ArrayablePayment::class) . "]");
        }

        $payment = ($payment instanceof ArrayablePayment) ? $payment->toPaymentArray() : $payment;

        if (isset($payment['paymentMethod']) && is_array($payment['paymentMethod']) && !empty($payment['paymentMethod'])) {
            $payment['paymentMethod'] = $payment['paymentMethod'][0];
        }

        return $payment;
    }

    /**
     * Adds single or multiple payments
     *
     * @see ArrayablePayment
     * @param array|ArrayablePayment|ArrayablePaymentCollection $payment
     */
    public function addPayment($payment)
    {

        if ($payment instanceof ArrayablePaymentCollection) {
            Collection::wrap($payment->toPaymentCollectionArray())->each(function ($payment) {
                $this->addPayment($payment);
            });
        }

        $this->payments->push($this->simplifyPayment($payment));
    }

    /**
     * @param array|ArrayablePaymentCollection $payments
     */
    public function addPayments($payments)
    {
        if ($payments instanceof ArrayablePaymentCollection) {
            $payments = $payments->toPaymentCollectionArray();
        }

        if (count($payments) > 0 && is_numeric(array_keys($payments)[0])) {
            foreach ($payments as $payment) {
                $this->addPayment($payment);
            }
        }
        else {
            $this->addPayment($payments);
        }
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->payments->isEmpty();
    }

    /**
     * @return bool
     */
    public function hasPayment()
    {
        return !$this->isEmpty();
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->payments->count();
    }

    /**
     * @return Collection
     */
    public function payments()
    {
        return $this->payments;
    }

    /**
     * @return array
     */
    public function paymentsToArray()
    {
        return $this->payments->toArray();
    }

}