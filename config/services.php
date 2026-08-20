<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'salla' => [
        'client_id' => env('SALLA_CLIENT_ID', ''),
        'client_secret' => env('SALLA_CLIENT_SECRET', ''),
        'api_url' => env('SALLA_API_URL', 'https://api.salla.dev/admin/v2'),
        'checkout_api_url' => env('SALLA_CHECKOUT_API_URL', 'https://api.salla.dev/store/v2/checkout'),
        'auth_url' => env('SALLA_AUTH_URL', 'https://accounts.salla.sa/oauth2/token'),
        'redirect_uri' => env('SALLA_REDIRECT_URI', ''),
        'merchant_id' => env('SALLA_MERCHANT_ID', ''),
        'store_identifier' => env('SALLA_STORE_IDENTIFIER', ''),
        'webhook_secret' => env('SALLA_WEBHOOK_SECRET', ''),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL', 'http://localhost:8000').'/auth/google/callback'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID', ''),
        'client_secret' => env('APPLE_CLIENT_SECRET', ''),
        'redirect' => env('APPLE_REDIRECT_URI', env('APP_URL', 'http://localhost:8000').'/auth/apple/callback'),
    ],

];
