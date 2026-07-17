<?php
// config/services.php
return [
    'mailgun' => ['domain' => env('MAILGUN_DOMAIN'), 'secret' => env('MAILGUN_SECRET'), 'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'), 'scheme' => 'https'],
    'postmark' => ['token' => env('POSTMARK_TOKEN')],
    'ses' => ['key' => env('AWS_ACCESS_KEY_ID'), 'secret' => env('AWS_SECRET_ACCESS_KEY'), 'region' => env('AWS_DEFAULT_REGION', 'us-east-1')],
    'razorpay' => [
        'key'    => env('RAZORPAY_KEY', 'rzp_test_XXXXXXXXXXXXXXXX'),
        'secret' => env('RAZORPAY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXX'),
    ],
    'upi' => [
        'id'   => env('UPI_ID', 'mdsayebalam10@okhdfcbank'),
        'name' => env('UPI_PAYEE_NAME', 'LibraryCRM'),
    ],
];
