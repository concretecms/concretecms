<?php

return [
    'adapters' => [
        'laminas' => [
            'loaders' => [
                'core' => 'Concrete\Core\Localization\Translator\Adapter\Laminas\Translation\Loader\Gettext\CoreTranslationLoader',
                'packages' => 'Concrete\Core\Localization\Translator\Adapter\Laminas\Translation\Loader\Gettext\PackagesTranslationLoader',
                'site' => 'Concrete\Core\Localization\Translator\Adapter\Laminas\Translation\Loader\Gettext\SiteTranslationLoader',
            ],
        ],
    ],
];
