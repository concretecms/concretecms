<?php

declare(strict_types=1);

namespace Concrete\Tests\Url\Resolver;

use Concrete\Core\Routing\Route;
use Concrete\Core\Routing\Router;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\PathUrlResolver;
use Concrete\Core\Url\Resolver\RouterUrlResolver;
use Concrete\TestHelpers\Url\Resolver\ResolverTestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class RouterUrlResolverTest extends ResolverTestCase
{
    /**
     * @var \Concrete\Core\Routing\Router
     */
    protected $router;

    public function setUp(): void
    {
        parent::setUp();
        $app = Application::getFacadeApplication();
        $this->router = $app->make(Router::class);
        $this->urlResolver = new RouterUrlResolver($app->make(PathUrlResolver::class), $this->router);
    }

    /**
     * Make sure that we can actually resolve a basic route.
     */
    public function testRoute()
    {
        $path = '/named/route/path';
        $name = 'named_route';
        $this->router->getRoutes()->add($name, new Route($path));
        $url = $this->canonicalUrlWithPath($path);
        static::assertEquals((string) $url, (string) $this->urlResolver->resolve(["route/{$name}"]));
    }

    /**
     * Test routes that have inline parameters.
     */
    public function testRouteWithParameters()
    {
        $path = '/named/{parameter}/route';
        $name = 'named_route';
        $value = uniqid();
        $this->router->getRoutes()->add($name, new Route($path));
        $url = $this->canonicalUrlWithPath(str_replace('{parameter}', $value, $path));
        static::assertEquals((string) $url, (string) $this->urlResolver->resolve(["route/{$name}", ['parameter' => $value]]));
    }

    /**
     * Test not finding a named route in the list.
     */
    public function testRouteMiss()
    {
        $resolved = uniqid();
        static::assertEquals($resolved, $this->urlResolver->resolve(['route/miss'], $resolved));
    }

    /**
     * Test not matching the expected syntax.
     */
    public function testNoMatch()
    {
        $resolved = uniqid();
        static::assertEquals($resolved, $this->urlResolver->resolve(['no match'], $resolved));
    }
}
