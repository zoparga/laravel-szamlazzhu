<?php
namespace zoparga\SzamlazzHu\Internal\Support;


trait CustomerTaxSubjects {

    /**
     * All the accepted tax subjects
     *
     * @var array
     */
    public static $taxSubjects = [
        'non_eu_company' => 7,
        'eu_company' => 6,
        'hungarian_tax_id' => 1,
        'unknown' => 0,
        'no_tax_id' => -1,
    ];

    /**
     * All the accepted tax subjects
     * @return array
     */
    protected function taxSubjects()
    {
        return static::$taxSubjects;
    }
}
