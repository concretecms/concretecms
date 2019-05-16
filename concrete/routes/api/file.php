<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

$router->get('/file/{fID}', '\Concrete\Core\File\Api\FilesController::read')
    ->setRequirement('fID' ,'[0-9]+')
    ->setScopes('files:read');
$router->get('/files', '\Concrete\Core\File\Api\FilesController::listFiles')
    ->setScopes('files:read');