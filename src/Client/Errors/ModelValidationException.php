<?php


namespace SzuniSoft\SzamlazzHu\Client\Errors;


use Exception;
use Illuminate\Contracts\Validation\Validator;
use SzuniSoft\SzamlazzHu\Internal\AbstractModel;
use Throwable;

abstract class ModelValidationException extends Exception
{

    /**
     * @var AbstractModel
     */
    protected $model;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * ModelValidationException constructor.
     * @param AbstractModel $model
     * @param Validator $validator
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(AbstractModel $model, Validator $validator, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->model = $model;
        $this->validator = $validator;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

}