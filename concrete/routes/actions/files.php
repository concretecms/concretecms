<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->all('/approve_version', 'File::approveVersion');
$router->all('/delete_version', 'File::deleteVersion');
$router->all('/duplicate', 'File::duplicate');
$router->all('/get_json', 'File::getJSON');
$router->all('/rescan', 'File::rescan');
$router->all('/rescan_multiple', 'File::rescanMultiple');
$router->all('/star', 'File::star');
$router->all('/upload', 'File::upload');
$router->all('/folder/add', 'File\Folder::add');
$router->all('/folder/contents', '\Concrete\Controller\Search\FileFolder::submit');
$router->all('/thumbnailer', 'File\Thumbnailer::generate');
