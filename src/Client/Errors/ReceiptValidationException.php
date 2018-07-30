<?php


namespace SzuniSoft\SzamlazzHu\Client\Errors;


use Illuminate\Contracts\Validation\Validator;
use SzuniSoft\SzamlazzHu\Internal\AbstractModel;
use SzuniSoft\SzamlazzHu\Receipt;
use Throwable;

class ReceiptValidationException extends ModelValidationException
{

    /**
     * ReceiptValidationException constructor.
     * @param Receipt $model
     * @param Validator $validator
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(Receipt $model, Validator $validator, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($model, $validator, $message, $code, $previous);
    }


    /**
     * @return Receipt|AbstractModel
     */
    public function getReceipt()
    {
        return $this->model;
    }

}