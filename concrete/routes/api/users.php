<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

$router->get('/users', '\Concrete\Core\Api\Controller\Users::listUsers')
    ->setScopes('users:read')
;

$router->get('/users/{uID}', '\Concrete\Core\Api\Controller\Users::read')
    ->setRequirement('uID', '[0-9]+')
    ->setScopes('users:read')
;

$router->post('/users', '\Concrete\Core\Api\Controller\Users::add')
    ->setScopes('users:add')
;

$router->put('/users/{uID}', '\Concrete\Core\Api\Controller\Users::update')
    ->setRequirement('uID', '[0-9]+')
    ->setScopes('users:update')
;

$router->post('/users/{uID}/change_password', '\Concrete\Core\Api\Controller\Users::changePassword')
    ->setRequirement('uID', '[0-9]+')
    ->setScopes('users:update')
;

$router->delete('/users/{uID}', '\Concrete\Core\Api\Controller\Users::delete')
    ->setRequirement('uID', '[0-9]+')
    ->setScopes('users:delete')
;
