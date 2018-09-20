<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->all('/bulk/properties', 'Bulk\Properties::view');
$router->all('/bulk/properties/clear_attribute', 'Bulk\Properties::clearAttribute');
$router->all('/bulk/properties/update_attribute', 'Bulk\Properties::updateAttribute');
$router->all('/search', 'Search::view');
$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/submit', 'AdvancedSearch::submit');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');

$router->all('/groupadd', 'Bulk\Group::groupadd');
$router->all('/groupadd/submit', 'Bulk\Group::groupaddsubmit');
$router->all('/groupremove', 'Bulk\Group::groupremove');
$router->all('/groupremove/submit', 'Bulk\Group::groupremovesubmit');
$router->all('/delete', 'Bulk\Delete::view');
$router->all('/delete/submit', 'Bulk\Delete::submit');
$router->all('/activate', 'Bulk\Activate::activate');
$router->all('/deactivate', 'Bulk\Activate::deactivate');
$router->all('/activate/submit', 'Bulk\Activate::activatesubmit');
$router->all('/deactivate/submit', 'Bulk\Activate::deactivatesubmit');


$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');