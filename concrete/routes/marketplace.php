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

$router->all('/ccm/system/dialogs/marketplace/checkout', '\Concrete\Controller\Dialog\Marketplace\Checkout::view');
$router->all('/ccm/system/dialogs/marketplace/download', '\Concrete\Controller\Dialog\Marketplace\Download::view');
$router->all('/ccm/system/marketplace/connect', '\Concrete\Controller\Backend\Marketplace\Connect::view');
$router->all('/ccm/system/marketplace/search', '\Concrete\Controller\Backend\Marketplace\Search::view');
