<?php

use Concrete\Core\System\Info;
use Concrete\Core\System\InfoTransformer;
use Concrete\Core\System\Status\QueueStatus;
use League\Fractal\Resource\Item;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/api/v1
 * Namespace: <none>
 */

$router->get('/system/info', function () {
    return new Item(new Info(), new InfoTransformer());
})->setScopes('system:info:read');

$router->get('/system/status/queue', function () use ($app) {
    $status = $app->make(QueueStatus::class);
    return new \League\Fractal\Resource\Item($status, false);
})->setScopes('system:queue:read');
