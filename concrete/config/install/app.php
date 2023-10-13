<?php

return [
    'routes' => [
        '/install' => ['\Concrete\Controller\Install::view'],
        '/install/begin_installation' => ['\Concrete\Controller\Install::begin_installation'],
        '/install/validate_environment' => ['\Concrete\Controller\Install::validate_environment'],
        '/install/select_language' => ['\Concrete\Controller\Install::select_language'],
        '/install/get_site_locale_countries/{viewLocaleID}/{languageID}/{preselectedCountryID}' => ['\Concrete\Controller\Install::get_site_locale_countries'],
        '/install/reload_preconditions' => ['\Concrete\Controller\Install::reloadPreconditions'],
        '/install/web_precondition/{handle}' => ['\Concrete\Controller\Install::web_precondition'],
        '/install/web_precondition/{handle}/{argument}' => ['\Concrete\Controller\Install::web_precondition'],
        '/install/configure' => ['\Concrete\Controller\Install::configure'],
        '/install/run_routine/{pkgHandle}' => ['\Concrete\Controller\Install::run_routine'],
        '/install/i18n/{locale}' => ['\Concrete\Controller\Install::getInstallerStrings'],
        '/install/{locale}' => ['\Concrete\Controller\Install::view'],
    ],

    'providers' => [
        'core_install' => '\Concrete\Core\Install\InstallServiceProvider',
    ],

    'assets' => [
        'core/installer' => [
            ['javascript', 'js/installer.js', ['minify' => false, 'combine' => false]],
            ['css', 'css/installer.css', ['minify' => false, 'combine' => false]],
        ],
    ],

    'asset_groups' => [
        'core/installer' => [
            [
                ['javascript', 'vue'],
                ['javascript', 'core/installer'],
                ['javascript', 'bootstrap'],
                ['css', 'core/installer'],
            ],
        ],
    ],


];