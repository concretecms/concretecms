<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

use Concrete\Core\File\FileList;
use Concrete\Core\File\File;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Permission\Checker;

$router->get('/file/{fID}', '\Concrete\Core\File\Api\FilesController::read')
    ->setRequirement('fID' ,'[0-9]+')
    ->setScopes('files:read');
$router->get('/files', '\Concrete\Core\File\Api\FilesController::listFiles')
    ->setScopes('files:read');