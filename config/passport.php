<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Passport Guard
    |--------------------------------------------------------------------------
    */
    'guard' => 'web',

    'middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    | Keys are stored as files by default. Can be overridden via env vars
    | (useful for containerized deployments where filesystem is ephemeral).
    */
    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Passport Database Connection
    |--------------------------------------------------------------------------
    */
    'connection' => env('PASSPORT_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Token Expiry
    |--------------------------------------------------------------------------
    | Access tokens expire after PASSPORT_TOKEN_EXPIRE_DAYS days.
    | Refresh tokens expire after PASSPORT_REFRESH_TOKEN_EXPIRE_DAYS days.
    */
    'token_expire_days'         => (int) env('PASSPORT_TOKEN_EXPIRE_DAYS', 1),
    'refresh_token_expire_days' => (int) env('PASSPORT_REFRESH_TOKEN_EXPIRE_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Password Grant Client
    |--------------------------------------------------------------------------
    | Used by LoginAction to issue tokens via the Password Grant flow.
    */
    'password_client_id'     => env('PASSPORT_CLIENT_ID'),
    'password_client_secret' => env('PASSPORT_CLIENT_SECRET'),

];
