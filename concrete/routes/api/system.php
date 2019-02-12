<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

use Concrete\Core\System\Info;
use Concrete\Core\System\Status\QueueStatus;

$router->get('/system/info', function() {
    return new \League\Fractal\Resource\Item(new Info(), new \Concrete\Core\System\InfoTransformer());
});

$router->get('/system/status/queue', function() use ($app){
    $status = $app->make(QueueStatus::class);
    // @TODO fix this.
    return new \League\Fractal\Resource\Item($status, false);
});
