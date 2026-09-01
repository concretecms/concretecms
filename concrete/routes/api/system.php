<?php

use Concrete\Core\System\Info;
use Concrete\Core\System\InfoTransformer;
use League\Fractal\Resource\Item;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

$router->get('/system/info', '\Concrete\Core\Api\Controller\System::info')->setScopes('system:info:read');
$router->get('/system/openapi', '\Concrete\Core\Api\Controller\System::openapi')->setScopes('system:openapi:read');
