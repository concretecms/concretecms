<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/page
 * Namespace: Concrete\Controller\Dialog\Page\
 */

$router->all('/add', 'Add::view');
$router->all('/versions', 'Versions::view');
$router->all('/versions/duplicate', 'Versions::duplicate');
$router->all('/versions/new_page', 'Versions::new_page');
$router->all('/versions/delete', 'Versions::delete');
$router->all('/versions/approve', 'Versions::approve');
$router->all('/versions/unapprove', 'Versions::unapprove');
$router->all('/add_block', 'AddBlock::view');
$router->all('/add_block/submit', 'AddBlock::submit');
$router->all('/add_block_list', 'AddBlockList::view');
$router->all('/add_external', 'AddExternal::view');
$router->all('/add_external/submit', 'AddExternal::submit');
$router->all('/add/compose/{ptID}/{cParentID}', 'Add\Compose::view');
$router->all('/add/compose/submit', 'Add\Compose::submit');
$router->all('/attributes', 'Attributes::view');
$router->all('/bulk/properties', 'Bulk\Properties::view');
$router->all('/bulk/properties/get_attribute', 'Bulk\Properties::getAttribute');
$router->all('/bulk/properties/submit', 'Bulk\Properties::submit');
$router->all('/bulk/delete', 'Bulk\Delete::view');
$router->all('/bulk/delete/submit', 'Bulk\Delete::submit');
$router->all('/bulk/permissions/get_all_access_entities', 'Bulk\Permissions::getAllAccessEntities');
$router->all('/bulk/permissions', 'Bulk\Permissions::view');
$router->all('/bulk/permissions/{task}', 'Bulk\Permissions::view');
$router->all('/clipboard', 'Clipboard::view');
$router->all('/delete', 'Delete::view');
$router->all('/delete/submit', 'Delete::submit');
$router->all('/delete_alias', 'DeleteAlias::view');
$router->all('/delete_alias/submit', 'DeleteAlias::submit');
$router->all('/delete_from_sitemap', 'Delete::viewFromSitemap');
$router->all('/design', 'Design::view');
$router->all('/design/submit', 'Design::submit');
$router->all('/design/css', 'Design\Css::view');
$router->all('/design/css/get', 'Design\Css::getCss');
$router->all('/design/css/set', 'Design\Css::setCss');
$router->all('/design/css/submit', 'Design\Css::submit');
$router->all('/edit_alias', 'EditAlias::view');
$router->all('/edit_alias/submit', 'EditAlias::submit');
$router->all('/edit_external', 'EditExternal::view');
$router->all('/edit_external/submit', 'EditExternal::submit');
$router->all('/location', 'Location::view');
$router->all('/seo', 'Seo::view');
$router->all('/sitemap_selector', 'SitemapSelector::view');
$router->all('/drag_request', 'DragRequest::view');
$router->all('/drag_request/submit', 'DragRequest::submit');
$router->all('/drag_request/copy_all', 'DragRequest::doCopyAll');
$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/submit', 'AdvancedSearch::submit');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');

$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');

$router->all('/summary_templates', 'SummaryTemplates::view');
$router->all('/summary_templates/submit', 'SummaryTemplates::submit');
