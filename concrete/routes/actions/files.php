<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Routing\Router $router */

/*
 * Base path: /ccm/system/file
 * Namespace: Concrete\Controller\Backend\
 */

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
$router->all('/folder/contents', '\Concrete\Controller\Search\FileFolder::submit');
$router->all('/thumbnailer', 'File\Thumbnailer::generate');
