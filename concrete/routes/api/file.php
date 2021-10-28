<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/api/v1
 * Namespace: <none>
 */

$router->get('/file/{fID}', '\Concrete\Core\File\Api\FilesController::read')
    ->setRequirement('fID', '[0-9]+')
    ->setScopes('files:read')
;
$router->get('/files/mine', '\Concrete\Core\File\Api\FilesController::getMyFiles')
    ->setScopes('files:read')
;
