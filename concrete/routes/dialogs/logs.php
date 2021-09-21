<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/logs
 * Namespace: Concrete\Controller\Dialog\Logs
 */

$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/submit', 'AdvancedSearch::submit');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');
$router->all('/bulk/delete', 'Bulk\Delete::view');
$router->all('/bulk/delete/submit', 'Bulk\Delete::submit');
$router->all('/delete', 'Delete::view');
$router->all('/delete/submit', 'Delete::submit');
$router->all('/delete_all', 'DeleteAll::view');
$router->all('/delete_all/submit', 'DeleteAll::submit');

$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');
