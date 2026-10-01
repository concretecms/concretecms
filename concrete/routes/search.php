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

$router->all('/ccm/system/search/pages/preset/{presetID}', '\Concrete\Controller\Search\Pages::searchPreset');

$router->all('/ccm/system/search/users/basic', '\Concrete\Controller\Search\Users::searchBasic');
$router->all('/ccm/system/search/users/current', '\Concrete\Controller\Search\Users::searchCurrent');
$router->all('/ccm/system/search/users/preset/{presetID}', '\Concrete\Controller\Search\Users::searchPreset');
$router->all('/ccm/system/search/users/clear', '\Concrete\Controller\Search\Users::clearSearch');
