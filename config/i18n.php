<?php

return [

    'services' => [
        'google' => [
            'translate' => [
                'key' => env('GOOGLE_TRANSLATE_KEY')
            ]
        ],
    ],
    'translator' => [
        'default_model' => env('TRANSLATOR_MODEL', 'google'),
    ],
];