<?php

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var \Concrete\Core\Routing\Router
 */
$router->get('/ccm/site/redirect/{siteID}', '\Concrete\Controller\Frontend\SelectSite::selectAndRedirect');
