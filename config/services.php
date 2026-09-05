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

    'chatbot' => [
        // API key untuk endpoint GET /api/chatbot/summary/{phone} (dipanggil
        // n8n). Kalau kosong/tidak di-set, endpoint tetap terbuka tanpa auth -
        // isi CHATBOT_API_KEY di .env & kirim via header X-API-Key dari n8n.
        'api_key' => env('CHATBOT_API_KEY'),
    ],

    'ekaldik' => [
        // API key untuk endpoint /api/ekaldik/* (dipanggil E-Kaldik/SIM Kurikulum).
        // Kalau kosong/tidak di-set, endpoint tetap terbuka tanpa auth.
        // Isi EKALDIK_API_KEY di .env & set key yang sama di E-Kaldik.
        'api_key'  => env('EKALDIK_API_KEY'),
        // Base URL E-Kaldik untuk sync hari libur
        'base_url' => env('EKALDIK_BASE_URL', ''),
    ],

    'phone_api' => [
        // API key untuk endpoint /api/phone/* (form update HP ortu dari domain lain).
        // Wajib diisi di .env production: PHONE_API_KEY=isi_token_rahasia_panjang
        'api_key'         => env('PHONE_API_KEY'),
        // Domain yang diizinkan akses CORS (domain form update HP)
        'allowed_origin'  => env('PHONE_API_ALLOWED_ORIGIN', 'https://wa.dmcenter.my.id'),
    ],

];
