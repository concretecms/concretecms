<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/workflow
 * Namespace: Concrete\Controller\Dialog\Workflow\
 */

$router
    ->all('/change_page_permissions/{wpID}', 'ChangePagePermissions::view')
    ->setRequirements([
        'wpID' => '[1-9]\d*',
    ])
;
