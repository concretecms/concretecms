<?php

declare(strict_types=1);

namespace Concrete\Tests\Api;

use Concrete\Core\Api\ApiRouteList;
use Concrete\Core\Routing\Router;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class ApiRouteListTest extends TestCase
{
    public function testBlockTypeRoutesAreRegistered(): void
    {
        $router = app(Router::class);
        $router->loadRouteList(new ApiRouteList());
        $paths = [];
        foreach ($router->getRoutes() as $route) {
            $paths[] = $route->getPath();
        }

        static::assertContains('/ccm/api/1.0/block_types', $paths);
        static::assertContains('/ccm/api/1.0/block_types/{blockTypeHandle}', $paths);
    }
}
