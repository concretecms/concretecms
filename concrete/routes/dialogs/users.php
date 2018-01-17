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