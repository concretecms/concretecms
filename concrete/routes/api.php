<?php

defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\System\Info;
use Concrete\Core\API\Transformer\InfoTransformer;
use League\Fractal\Resource\Item;
$app = \Concrete\Core\Support\Facade\Facade::getFacadeApplication();

use Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider;

/**
 * @var $router \Concrete\Core\Routing\Router
 */
$router->get('/system/info', function() {
    $info = new Info();
    return new Item($info, new InfoTransformer());
});

$router->get('/site/trees', function() use ($app) {
    $provider = $app->make(StandardSitemapProvider::class);
    $collection = $provider->getTreeCollection();
    return new Item($collection, new \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionTransformer());
});
