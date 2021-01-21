<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/backend/dashboard
 * Namespace: Concrete\Controller\Backend\Dashboard
 */

$router
    ->all('get_image_data', 'GetImageData::view')
;
$router
    ->all('sitemap_check_in', 'SitemapCheckIn::view')
;
$router
    ->all('sitemap_update', 'SitemapUpdate::view')
;
