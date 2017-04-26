<?php

return array(

    'routes' => array(
        '/install' => array('\Concrete\Controller\Install::view'),
        '/install/select_language' => array('\Concrete\Controller\Install::select_language'),
        '/install/setup' => array('\Concrete\Controller\Install::setup'),
        '/install/get_site_locale_countries/{viewLocaleID}/{languageID}/{preselectedCountryID}' => array('\Concrete\Controller\Install::get_site_locale_countries'),
        '/install/test_url/{num1}/{num2}' => array('\Concrete\Controller\Install::test_url'),
        '/install/configure' => array('\Concrete\Controller\Install::configure'),
        '/install/run_routine/{pkgHandle}/{routine}' => array('\Concrete\Controller\Install::run_routine'),
    ),

);
