<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

$router->get('/block_types', '\Concrete\Core\Api\Controller\BlockTypes::listBlockTypes')
    ->setScopes('block_types:read')
;

$router->get('/block_types/{blockTypeHandle}', '\Concrete\Core\Api\Controller\BlockTypes::read')
    ->setRequirement('blockTypeHandle', '[A-Za-z0-9_]+')
    ->setScopes('block_types:read')
;
