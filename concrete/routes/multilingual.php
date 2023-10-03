<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/frontend/multilingual
 * Namespace: Concrete\Controller\Frontend
 */

$router
    ->get('/switch_language/{currentPageID}/{targetSectionID}', 'SwitchLanguage::switchLanguage')
    ->setName('switch_language')
    ->setRequirements(['currentPageID' => '[0-9]+', 'targetSectionID' => '[0-9]+'])
;