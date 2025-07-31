<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Routing\Router $router
 * @var Concrete\Core\Application\Application $app
 */

/*
 * Base path: <none>
 * Namespace: <none>
 */

$sitemapXMLPath = '/' . ltrim(str_replace(DIRECTORY_SEPARATOR, '/', (string) $app['config']->get('concrete.sitemap_xml.file')), '/');
$router->all($sitemapXMLPath, '\Concrete\Controller\Frontend\Sitemap::view');