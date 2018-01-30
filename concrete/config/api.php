<?php

return [
    'site' => [
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
    ],
    'system' => [
        'operations' => [
            'getSystemInformation' => [
                'httpMethod' => 'GET',
                'uri' => 'info',
                'responseModel' => 'infoResponse',
                'parameters' => []
            ]
        ],
        'models' => [
            'infoResponse' => [
                'type' => 'object',
                'additionalProperties' => [
                    'location' => 'json'
                ]
            ]
        ]
    ]
];
