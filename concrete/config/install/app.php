<?php

return [
    'routes' => [
        '/install' => ['\Concrete\Controller\Install::view'],
        '/install/select_language' => ['\Concrete\Controller\Install::select_language'],
        '/install/setup' => ['\Concrete\Controller\Install::setup'],
        '/install/get_site_locale_countries/{viewLocaleID}/{languageID}/{preselectedCountryID}' => ['\Concrete\Controller\Install::get_site_locale_countries'],
        '/install/web_precondition/{handle}' => ['\Concrete\Controller\Install::web_precondition'],
        '/install/web_precondition/{handle}/{argument}' => ['\Concrete\Controller\Install::web_precondition'],
        '/install/configure' => ['\Concrete\Controller\Install::configure'],
        '/install/run_routine/{pkgHandle}/{routine}' => ['\Concrete\Controller\Install::run_routine'],
    ],
];
