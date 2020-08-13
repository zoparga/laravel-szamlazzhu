# Configuration

To export and override defaults please use the following command.
```bash
php artisan vendor:publish --provider="SzuniSoft\SzamlazzHu\Providers\SzamlazzHuServiceProvider" --tag="config"
```

## Sample
This is the generated configuration file _(config/szamlazz-hu.php)_.

```php
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
```

## Default merchant
The default merchant can be setup in the configuration.
The given merchant settings will be applied on the invoices automatically when the merchant is not specified on the invoice directly.

## Client
### Credentials
- Credentials are mandatory and required.
- You can use your custom certificate. If it is provided you no longer need to specify password.

### Behavior
- Timeout is configurable however it is not recommended.
- Leave base URI untouched.


### Storage
Package can save PDF files automatically. You can specify your storage disk and the prefix path. If you don't want to save PDF files please turn off the auto save flag _'**auto_save' => false**_.

Please note that obtaining and saving PDF files can increase the request time with about _**~500-1500ms**_.
