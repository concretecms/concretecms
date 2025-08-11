<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

$router->get('/groups', '\Concrete\Core\Api\Controller\Groups::listGroups')
    ->setScopes('groups:read')
;

$router->get('/groups/{gID}', '\Concrete\Core\Api\Controller\Groups::read')
    ->setRequirement('gID', '[0-9]+')
    ->setScopes('groups:read')
;

$router->post('/groups', '\Concrete\Core\Api\Controller\Groups::add')
    ->setRequirement('gID', '[0-9]+')
    ->setScopes('groups:add')
;
