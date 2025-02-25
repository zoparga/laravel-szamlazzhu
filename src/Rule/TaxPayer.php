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

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = App::make('szamlazz-hu.client');
        $response = $client->queryTaxPayer((string) $value);
        return $response->taxpayerValidity;
    }

    /**
     * @return string
     */
    public function message()
    {
        return trans('laravel-szamlazzhu::validation.message');
    }
}
