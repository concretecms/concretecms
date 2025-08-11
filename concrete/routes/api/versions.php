<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */


$router->get('/page_versions/{pageID}/{versionID}', '\Concrete\Core\Api\Controller\Versions::read')
    ->setRequirement('pageID', '[0-9]+')
    ->setRequirement('versionID', '[0-9]+')
    ->setScopes('pages:versions:read')
;

$router->get('/page_versions/{pageID}', '\Concrete\Core\Api\Controller\Versions::listVersions')
    ->setRequirement('pageID', '[0-9]+')
    ->setScopes('pages:versions:read')
;

$router->delete('/page_versions/{pageID}/{versionID}', '\Concrete\Core\Api\Controller\Versions::delete')
    ->setRequirement('pageID', '[0-9]+')
    ->setRequirement('versionID', '[0-9]+')
    ->setScopes('pages:versions:delete')
;

$router->put('/page_versions/{pageID}/{versionID}', '\Concrete\Core\Api\Controller\Versions::update')
    ->setRequirement('pageID', '[0-9]+')
    ->setRequirement('versionID', '[0-9]+')
    ->setScopes('pages:versions:update')
;
