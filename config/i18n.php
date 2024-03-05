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
    ],
    'translator' => [
        'default_model' => env('TRANSLATOR_MODEL', 'google'),
    ],
    'domain'    =>  [
        'id'    =>  env('I18N_DOMAIN_ID')
    ],
    'translations'  =>  [
        'folder'    =>  base_path('/lang/vue/')
    ]
];
