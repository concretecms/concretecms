<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/dialogs/file
 * Namespace: Concrete\Controller\Dialog\File
 */

$router->all('/upload_complete', 'UploadComplete::view');
$router->all('/bulk/delete', 'Bulk\Delete::view');
$router->all('/bulk/delete/delete_files', 'Bulk\Delete::deleteFiles');
$router->all('/bulk/properties', 'Bulk\Properties::view');
$router->all('/bulk/sets', 'Bulk\Sets::view');
$router->all('/bulk/sets/submit', 'Bulk\Sets::submit');
$router->all('/bulk/folder', 'Bulk\Folder::view');
$router->all('/bulk/folder/submit', 'Bulk\Folder::submit');
$router->all('/bulk/properties/get_attribute', 'Bulk\Properties::getAttribute');
$router->all('/bulk/properties/submit', 'Bulk\Properties::submit');
$router->all('/bulk/storage', 'Bulk\Storage::view');
$router->all('/bulk/storage/submit', 'Bulk\Storage::submit');
$router->all('/sets', 'Sets::view');
$router->all('/sets/submit', 'Sets::submit');
$router->all('/folder', 'Folder::view');
$router->all('/folder/submit', 'Folder::submit');
$router->all('/properties/', 'Properties::view');
$router->all('/properties/submit', 'Properties::submit');
$router->all('/properties/get_attribute', 'Properties::getAttribute');
$router->all('/versions/', 'Versions::view');
$router->all('/delete/{fID}', 'Delete::view');
$router->all('/delete/{fID}/submit', 'Delete::submit');

$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');
$router->all('/search', 'Search::view');
$router->all('/jump_to_folder', 'JumpToFolder::view');
$router->all('/thumbnails', 'Thumbnails::view');
$router->all('/thumbnails/edit', 'Thumbnails\Edit::view');
$router->all('/usage/{fID}', 'Usage::view');
$router->all('/statistics/{fID}', 'Statistics::view');
$router->all('/statistics/load_more/{fID}', 'Statistics::load_more');
$router->all('/statistics/download/{fID}', 'Statistics::download');

$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');

$router->all('/import', 'Import::view');
$router->all('/replace', 'Replace::view');
