<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: <none>
 * Namespace: <none>
 */

$router->all('/ccm/system/attribute/action/{action}', 'Concrete\Controller\Backend\Attribute\Action::dispatch')
    ->setName('attribute_action')
    ->setRequirements(['action' => '.+'])
;
$router->all('/ccm/system/attributes/attribute_sort/set', 'Concrete\Controller\Backend\Attributes::sortInSet');
