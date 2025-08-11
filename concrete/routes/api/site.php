<?php

use Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

$router->get('/sites', '\Concrete\Core\Api\Controller\Sites::listSites')
    ->setScopes('sites:read')
;

$router->get('/sites/default', '\Concrete\Core\Api\Controller\Sites::getDefault')
    ->setScopes('sites:read')
;

$router->get('/sites/{siteID}', '\Concrete\Core\Api\Controller\Sites::read')
    ->setScopes('sites:read')
;