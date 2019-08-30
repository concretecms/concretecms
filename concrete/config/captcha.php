<?php

return [
    'recaptcha_v3' => [
        'url' => [
            'keys_source' => 'https://www.google.com/recaptcha/admin',
            'javascript_asset' => 'https://www.google.com/recaptcha/api.js?render=explicit&onload=RecaptchaV3',
            'verify' => 'https://www.google.com/recaptcha/api/siteverify',
        ],
        'site_key' => '',
        'secret_key' => '',
        'score' => 0.5,
        'position' => 'bottomright',
        'log_score' => false,
        'send_ip' => false,
    ],
];
