<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'duitku' => [
        'merchant_code' => env('DUITKU_MERCHANT_CODE'),
        'api_key' => env('DUITKU_API_KEY'),
        'environment' => env('DUITKU_ENV', 'sandbox'),
        'expiry_period' => (int) env('DUITKU_EXPIRY_PERIOD', 60),
        'sandbox' => [
            'create_invoice_url' => 'https://api-sandbox.duitku.com/api/merchant/createInvoice',
            'transaction_status_url' => 'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus',
        ],
        'production' => [
            'create_invoice_url' => 'https://api-prod.duitku.com/api/merchant/createInvoice',
            'transaction_status_url' => 'https://passport.duitku.com/webapi/api/merchant/transactionStatus',
        ],
    ],

];
