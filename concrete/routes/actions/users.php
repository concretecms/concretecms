<?php

defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->all('/add_group', 'User::addGroup');
$router->all('/remove_group', 'User::removeGroup');
$router->all('/get_json', 'User::getJSON');
