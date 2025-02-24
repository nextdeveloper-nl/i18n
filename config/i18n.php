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
                'key' => env('GOOGLE_TRANSLATE_KEY')
            ]
        ],
        'openai'    => [
            'url'   => env('OPENAI_URL', 'https://api.openai.com/v1/'),
            'key'   => env('OPENAI_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4'),
        ],
    ],
    'translator' => [
        'default_model' => env('TRANSLATOR_MODEL', 'openai'),
    ],
    'domain'    =>  [
        'id'    =>  env('I18N_DOMAIN_ID')
    ],
    'translations'  =>  [
        'folder'    =>  base_path('/lang/vue/')
    ]
];
