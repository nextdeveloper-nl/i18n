<?php

return [
    'scopes'    =>  [
        'global'    =>  [
            '\NextDeveloper\IAM\Database\Scopes\AuthorizationScope'
        ]
    ],
    'services' => [
        'google' => [
            'translate' => [
                'key' => env('GOOGLE_TRANSLATE_KEY', 'AIzaSyDoadNi3qjS3USUAH8QS71kNtT5HbrZ1a0')
            ]
        ],
    ],
    'translator' => [
        'default_model' => env('TRANSLATOR_MODEL', 'google'),
    ],
];
