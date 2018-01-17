<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */

$router->all('/ccm/system/dialogs/express/entry/search', '\Concrete\Controller\Dialog\Express\Search::entries');
$router->all('/ccm/system/search/express/entries/submit/{entityID}', '\Concrete\Controller\Search\Express\Entries::submit');
$router->all('/ccm/system/express/entry/get_json', '\Concrete\Controller\Backend\Express\Entry::getJSON');
$router->all('/ccm/system/dialogs/express/advanced_search/', '\Concrete\Controller\Dialog\Express\AdvancedSearch::view');
$router->all('/ccm/system/dialogs/express/advanced_search/add_field/', '\Concrete\Controller\Dialog\Express\AdvancedSearch::addField');
$router->all('/ccm/system/dialogs/express/advanced_search/submit', '\Concrete\Controller\Dialog\Express\AdvancedSearch::submit');
