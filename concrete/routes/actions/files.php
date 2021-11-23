<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/file
 * Namespace: Concrete\Controller\Backend
 */

$router->all('/view', 'File\View::view');
$router->all('/approve_version', 'File::approveVersion');
$router->all('/delete_version', 'File::deleteVersion');
$router->all('/duplicate', 'File::duplicate');
$router->all('/get_json', 'File::getJSON');
$router->all('/rescan', 'File::rescan');
$router->all('/rescan_multiple', 'File::rescanMultiple');
$router->all('/star', 'File::star');
$router->all('/upload', 'File::upload');
$router->all('/download', 'File::download');
$router->all('/import_incoming', 'File::importIncoming');
$router->all('/import_remote', 'File::importRemote');
$router->all('/thumbnailer', 'File\Thumbnailer::generate');
$router->all('/fetch_incoming_files', 'File::fetchIncomingFiles');
$router->all('/fetch_directories', 'File::fetchDirectories');
$router->all('/create_directory', 'File::createDirectory');
$router->all('/edit', 'File\Edit::view');
$router->all('/edit/save/{fID}', 'File\Edit::save');
$router->all('/permissions', 'File\Permissions::view');
$router->post('/permissions/set_password', 'File\Permissions::setPassword');
$router->post('/permissions/set_location', 'File\Permissions::setLocation');
$router->all('/importers/imageeditor', 'File\Importer\ImageEditor::view');
$router->all('/importers/thumbnail', 'File\Importer\Thumbnail::view');
$router->all('/upload_complete', 'File::uploadComplete');
$router->all('/get_favorite_folders', 'File::getFavoriteFolders');
$router->all('/add_favorite_folder/{folderId}', 'File::addFavoriteFolder');
$router->all('/remove_favorite_folder/{folderId}', 'File::removeFavoriteFolder');

$router->all('/chooser/recent', 'File\Chooser::getRecent');
$router->all('/chooser/get_file_sets', 'File\Chooser::getFileSets');
$router->all('/chooser/get_file_set/{id}', 'File\Chooser::getFileSetFiles');
$router->all('/chooser/get_search_presets', 'File\Chooser::getSearchPresets');
$router->all('/chooser/get_search_preset/{id}', 'File\Chooser::getSearchPresetFiles');
$router->all('/chooser/get_folder_files', 'File\Chooser::getFolderFiles');
$router->all('/chooser/get_folder_files/{folderId}', 'File\Chooser::getFolderFiles');
$router->all('/chooser/get_breadcrumb', 'File\Chooser::getBreadcrumb');
$router->all('/chooser/get_breadcrumb/{folderId}', 'File\Chooser::getBreadcrumb');
$router->all('/chooser/search/{keyword}', 'File\Chooser::searchFiles');

$router->all('/chooser/external_file_provider/{externalFileProviderId}/import_file/{fileId}', 'File\Chooser::importExternal');
$router->all('/chooser/external_file_provider/{externalFileProviderId}/search/{keyword}', 'File\Chooser::searchExternal');
$router->all('/chooser/external_file_provider/{externalFileProviderId}/get_file_types', 'File\Chooser::getExternalFileTypes');
