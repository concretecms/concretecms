<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/user
 * Namespace: Concrete\Controller\Dialog\User
 */

$router->all('/bulk/properties', 'Bulk\Properties::view');
$router->all('/bulk/properties/get_attribute', 'Bulk\Properties::getAttribute');
$router->all('/bulk/properties/submit', 'Bulk\Properties::submit');
$router->all('/search', 'Search::view');
$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/submit', 'AdvancedSearch::submit');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');

$router->all('/bulk/groupadd', 'Bulk\Group::groupadd');
$router->all('/bulk/groupadd/submit', 'Bulk\Group::groupaddsubmit');
$router->all('/bulk/groupremove', 'Bulk\Group::groupremove');
$router->all('/bulk/groupremove/submit', 'Bulk\Group::groupremovesubmit');
$router->all('/bulk/delete', 'Bulk\Delete::view');
$router->all('/bulk/delete/submit', 'Bulk\Delete::submit');
$router->all('/bulk/activate', 'Bulk\Activate::activate');
$router->all('/bulk/deactivate', 'Bulk\Activate::deactivate');
$router->all('/bulk/activate/submit', 'Bulk\Activate::activatesubmit');
$router->all('/bulk/deactivate/submit', 'Bulk\Activate::deactivatesubmit');

$router->all('/attributes/{uID}', 'Attributes::view');
$router->all('/attributes/{uID}/get_attribute', 'Attributes::getAttribute');
$router->all('/attributes/{uID}/submit', 'Attributes::submit');

$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');

$router->get('/details', 'Details::view');
