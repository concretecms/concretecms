<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */

$router->get('/ccm/site/redirect/{siteID}', '\Concrete\Controller\Frontend\SelectSite::selectAndRedirect');
