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
        'id'    =>  'd2c98bdf-9942-11ee-b8af-c2ea10853885'
    ],
    'translations'  =>  [
        'folder'    =>  base_path('/lang/vue/')
    ]
];
