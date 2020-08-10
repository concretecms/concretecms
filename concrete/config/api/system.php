<?php

return [
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
];
