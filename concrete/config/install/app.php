<?php

return [
    'routes' => [
        '/install' => ['\Concrete\Controller\Install::view'],
        '/install/select_language' => ['\Concrete\Controller\Install::select_language'],
        '/install/setup' => ['\Concrete\Controller\Install::setup'],
        '/install/test_url/{num1}/{num2}' => ['\Concrete\Controller\Install::test_url'],
        '/install/configure' => ['\Concrete\Controller\Install::configure'],
        '/install/run_routine/{pkgHandle}/{routine}' => ['\Concrete\Controller\Install::run_routine'],
    ],
];
