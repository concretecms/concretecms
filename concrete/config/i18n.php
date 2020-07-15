<?php

return [
    'adapters' => [
        'zend' => [
            'loaders' => [
                'core' => 'Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\CoreTranslationLoader',
                'packages' => 'Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\PackagesTranslationLoader',
                'site' => 'Concrete\Core\Localization\Translator\Adapter\Zend\Translation\Loader\Gettext\SiteTranslationLoader',
            ],
        ],
    ],
];
