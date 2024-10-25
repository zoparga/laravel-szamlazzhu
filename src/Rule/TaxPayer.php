<?php

namespace zoparga\SzamlazzHu\Rule;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;

class TaxPayer implements Rule
{

    /**
     * @var array
     */
    protected $data = [];

    protected $message = 'The :attribute is invalid.';

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = App::make('szamlazzhu.client');
        $response = $client->queryTaxPayer((string) $value);
        $this->message = $response->errorMessage ?? 'The :attribute is invalid.';
        return $response->taxpayerValidity;
    }

    /**
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}