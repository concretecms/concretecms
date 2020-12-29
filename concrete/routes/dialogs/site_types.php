<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/site_type
 * Namespace: Concrete\Controller\Dialog\SiteType
 */

$router->all('/attributes/{stID}', 'Attributes::view');
$router->all('/attributes/{stID}/get_attribute', 'Attributes::getAttribute');
$router->all('/attributes/{stID}/submit', 'Attributes::submit');
