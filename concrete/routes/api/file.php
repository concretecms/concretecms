<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */
$router->get('/file/{fID}', '\Concrete\Core\File\Api\FilesController::read')
    ->setRequirement('fID', '[0-9]+')
    ->setScopes('files:read');
