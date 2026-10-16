<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Schooly Shop Business Rules Configuration
    |--------------------------------------------------------------------------
    */

    'payment_expiry_minutes' => (int) env('PAYMENT_EXPIRY_MINUTES', 60),
    'auto_confirm_hours' => (int) env('AUTO_CONFIRM_HOURS', 48),
    'return_window_hours' => (int) env('RETURN_WINDOW_HOURS', 48),

    'origin' => [
        'area_id' => env('SCHOOL_ORIGIN_AREA_ID'),
        'name' => env('SCHOOL_ORIGIN_NAME', 'Schooly Shop'),
        'phone' => env('SCHOOL_ORIGIN_PHONE'),
        'address' => env('SCHOOL_ORIGIN_ADDRESS'),
        'postal_code' => env('SCHOOL_ORIGIN_POSTAL'),
    ],
];
