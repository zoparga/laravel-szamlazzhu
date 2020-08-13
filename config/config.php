<?php

return [

    /*
     * These merchant details will be used by default.
     * You can override these values.
     * */
    'merchant' => [
        'bank_name' => env('SZAMLAZZ_HU_MERCHANT_BANK_NAME'),
        'bank_account_number' => env('SZAMLAZZ_HU_MERCHANT_BANK_ACCOUNT_NUMBER'),
        'reply_email' => env('SZAMLAZZ_HU_MERCHANT_REPLY_EMAIL')
    ],

    /*
     * API Client settings
     */
    'client' => [

        /*
         * Authentication credentials.
         * */
        'credentials' => [
            'api_key' => env('SZAMLAZZ_HU_API_KEY'),
            'username' => env('SZAMLAZZ_HU_USERNAME'),
            'password' => env('SZAMLAZZ_HU_PASSWORD')
        ],

        /*
         * You can enable the certificate based communication.
         * You do not need to provide password if you'll use szamlazz.hu's own certificate
         * */
        'certificate' => [
            'enabled' => false,
            'disk' => 'local',
            'path' => 'szamlazzhu/cacert.pem' // Relative to disk root
        ],

        /*
         * HTTP request timeout (in seconds)
         */
        'timeout' => 30,

        /*
         * Base URI used to reach API
         * */
        'base_uri' => env('SZAMLAZZ_HU_BASE_URI', 'https://www.szamlazz.hu/'),

        /*
         * Client can automatically save / update invoice PDF files if enabled
         * */
        'storage' => [
            'auto_save' => true,
            'disk' => 'local',
            'path' => 'szamlazzhu'
        ],

    ]
];
