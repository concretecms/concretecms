<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $router \Concrete\Core\Routing\Router
 * @var $app \Concrete\Core\Application\Application
 */

use Concrete\Core\System\Info;

$router->get('/system/info', function() {
    return new \League\Fractal\Resource\Item(new Info(), new \Concrete\Core\System\InfoTransformer());
});