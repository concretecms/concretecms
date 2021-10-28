<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/system/user
 * Namespace: Concrete\Controller\Backend
 */

$router->all('/add_group', 'User::addGroup');
$router->all('/remove_group', 'User::removeGroup');
$router->all('/get_json', 'User::getJSON');

$router->all('/chooser/search/{keyword}', 'User\Chooser::searchUsers');
