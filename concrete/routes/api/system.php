<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */
use Concrete\Core\System\Info;
use Concrete\Core\System\InfoTransformer;
use League\Fractal\Resource\Item;
use Concrete\Core\System\Status\QueueStatus;

$router->get('/system/info', function () {
    return new Item(new Info(), new InfoTransformer());
})->setScopes('system:info:read');

$router->get('/system/status/queue', function() use ($app){
    $status = $app->make(QueueStatus::class);
    // @TODO fix this.
    return new \League\Fractal\Resource\Item($status, false);
})->setScopes('system:queue:read');
