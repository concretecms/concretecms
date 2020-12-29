<?php

use Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Application $app
 * @var Concrete\Core\Routing\Router $router
 */

/*
 * Base path: /ccm/api/v1
 * Namespace: <none>
 */

$router->get('/site/trees', function () use ($app) {
    $provider = $app->make(StandardSitemapProvider::class);
    $collection = $provider->getTreeCollection();

    return new \League\Fractal\Resource\Item(
        $collection,
        new \Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionTransformer()
    );
})->setScopes('site:trees:read');
