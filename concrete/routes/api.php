<?php

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\System\Info;
use Concrete\Core\API\Transformer\InfoTransformer;
use League\Fractal\Resource\Item;

/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->get('/system/info', function() {
    $info = new Info();
    return new Item($info, new InfoTransformer());
});
