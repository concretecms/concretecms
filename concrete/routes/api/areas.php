<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */


$router->post('/pages/{pageID}/{areaHandle}', '\Concrete\Core\Api\Controller\Areas::addBlock')
    ->setRequirement('pageID', '[0-9]+')
    ->setScopes('pages:areas:add_block')
;

$router->delete('/pages/{pageID}/{areaHandle}/{blockID}', '\Concrete\Core\Api\Controller\Areas::deleteBlock')
    ->setRequirement('pageID', '[0-9]+')
    ->setRequirement('blockID', '[0-9]+')
    ->setScopes('pages:areas:delete_block')
;

$router->put('/pages/{pageID}/{areaHandle}/{blockID}', '\Concrete\Core\Api\Controller\Areas::updateBlock')
    ->setRequirement('pageID', '[0-9]+')
    ->setRequirement('blockID', '[0-9]+')
    ->setScopes('pages:areas:update_block')
;