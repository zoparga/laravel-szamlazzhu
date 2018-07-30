<?php


namespace SzuniSoft\SzamlazzHu\Internal\Support;


trait NormalizeParsedNumericArrays
{

    /**
     * @param array $array
     * @return array
     */
    protected function normalizeToNumericArray(array $array)
    {

        if (empty($array)) {
            return [];
        }

        return is_numeric(array_keys($array)[0]) ? $array : [$array];
    }

}