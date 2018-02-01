<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

use Concrete\Core\System\Info;
use Concrete\Core\System\Status\QueueStatus;

$router->get('/system/info', function() {
    return new Info();
});

$router->get('/system/status/queue', function() use ($app){
    return $app->make(QueueStatus::class);
});
