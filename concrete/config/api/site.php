<?php

return [
    'operations' => [
        'getSiteTrees' => [
            'uri' => 'trees',
            'httpMethod' => 'GET',
            'responseModel' => 'SiteTreeList',
            'parameters' => []
        ],
    ],
    'models' => [
        'SiteTreeList' => [
            'type' => 'object',
            'additionalProperties' => [
                'location' => 'json'
            ]
        ]
    ]
];
