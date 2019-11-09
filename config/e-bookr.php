<?php

return [
    'location_id'        => env('EBOOKR_LOCATION_ID', 0),
    'booking_aggregator' => [
        'driver'                             => env('EBOOKR_AGGREGATOR', 'smoobu'),
        'smoobu_api_url'                     => env('SMOOBU_API_URL'),
        'smoobu_api_key'                     => env('SMOOBU_API_KEY', null),
        'smoobu_settings_channel_id'         => env('SMOOBU_API_SETTINGS_CHANNEL_ID', null),
        'smoobu_webhook_url'                 => env('SMOOBU_WEBHOOK_URL', null),
        'smoobu_blocked_settings_channel_id' => env('SMOOBU_API_BLOCKED_SETTINGS_CHANNEL_ID', null),
    ],
];