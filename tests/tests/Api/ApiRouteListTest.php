<?php
namespace Concrete\Tests\Api;

use Concrete\Core\Api\ApiRouteList;
use Concrete\Core\Routing\Router;
use Concrete\Tests\TestCase;

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

        $this->assertContains('/ccm/api/1.0/block_types', $paths);
        $this->assertContains('/ccm/api/1.0/block_types/{blockTypeHandle}', $paths);
    }
}
