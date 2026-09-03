<?php

namespace Concrete\Tests\Http;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Level\ExpensiveCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\Early404Middleware;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Routing\RouteActionFactory;
use Concrete\Core\Routing\Router;
use Concrete\Tests\TestCase;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

class Early404MiddlewareTest extends TestCase
{
    public function testMatchingRoutePrefixDelegatesRequest(): void
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $router->get('/ccm/system/page/{id}', static function () {
        });
        $cache = Mockery::mock(ExpensiveCache::class);
        $connection = Mockery::mock(Connection::class);
        $delegate = Mockery::mock(DelegateInterface::class);
        $request = Request::create('https://www.concretecms.org/ccm/system/page/42');
        $response = new Response('ok');

        $delegate->shouldReceive('next')->once()->with($request)->andReturn($response);
        $connection->shouldNotReceive('fetchOne');

        $middleware = new Early404Middleware($cache, $router, $connection);

        $this->assertSame($response, $middleware->process($request, $delegate));
    }

    public function testMatchingPagePathPrefixDelegatesRequest(): void
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $cache = Mockery::mock(ExpensiveCache::class);
        $connection = Mockery::mock(Connection::class);
        $delegate = Mockery::mock(DelegateInterface::class);
        $request = Request::create('https://www.concretecms.org/blog/article/example');
        $response = new Response('ok');

        $connection->shouldReceive('fetchOne')
            ->once()
            ->with('select 1 from PagePaths where cPath = ? or cPath like ? limit 1', ['/blog', '/blog/%'])
            ->andReturn(1);
        $delegate->shouldReceive('next')->once()->with($request)->andReturn($response);

        $middleware = new Early404Middleware($cache, $router, $connection);

        $this->assertSame($response, $middleware->process($request, $delegate));
    }

    public function testMissingRouteAndPagePathReturnsNotFound(): void
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $router->get('/{slug}', static function () {
        });
        $cache = Mockery::mock(ExpensiveCache::class);
        $connection = Mockery::mock(Connection::class);
        $responseFactory = Mockery::mock(ResponseFactoryInterface::class);
        $app = Mockery::mock(Application::class);
        $delegate = Mockery::mock(DelegateInterface::class);
        $request = Request::create('https://www.concretecms.org/wp-login.php');
        $response = new Response('missing', 404);

        $connection->shouldReceive('fetchOne')
            ->once()
            ->with('select 1 from PagePaths where cPath = ? or cPath like ? limit 1', ['/wp-login.php', '/wp-login.php/%'])
            ->andReturn(false);
        $delegate->shouldNotReceive('next');
        $responseFactory->shouldReceive('cachedNotFound')->once()->withNoArgs()->andReturn($response);
        $app->shouldReceive('make')->once()->with(ResponseFactoryInterface::class)->andReturn($responseFactory);

        $middleware = new Early404Middleware($cache, $router, $connection);
        $middleware->setApplication($app);

        $this->assertSame($response, $middleware->process($request, $delegate));
    }
}
