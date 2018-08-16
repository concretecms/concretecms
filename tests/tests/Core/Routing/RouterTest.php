<?php
namespace Concrete\Tests\Core\Routing;


use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Request;
use Concrete\Core\Routing\ControllerRouteAction;
use Concrete\Core\Routing\Route;
use Concrete\Core\Routing\RouteActionFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Routing\Router;
use Concrete\Core\Routing\ClosureRouteAction;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Concrete\Core\Routing\MatchedRoute;
class TestController
{

    public function hello()
    {
        return 'oh hai';
    }
}

class TestMiddleware
{

}

class AnotherTestMiddleware
{

}

class RouterTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateRouter()
    {
        $app = Facade::getFacadeApplication();
        $router = $app->make('router');
        $this->assertInstanceOf(Router::class, $router);
    }

    public function testBasicRouteBuilder()
    {
        $app = Facade::getFacadeApplication();
        /**
         * @var $router Router
         */
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $router->get('/hello-world', function() { return 'hello world.'; });
        $routes = $router->getRoutes();
        $this->assertCount(1, $routes);

        $router->post('/api/update', function() { return 'what';})->setName('update_api');
        $this->assertCount(2, $routes);

        $route = $router->getRoutes()->get('update_api');
        $this->assertInstanceOf(Route::class, $route);

        $this->assertEquals('/api/update/', $route->getPath());
        $methods = $route->getMethods();
        $this->assertCount(1, $methods);
        $this->assertEquals('POST', $methods[0]);

        $route = $router->all('/my/method', 'Something\Controller')->getRoute();
        $this->assertCount(7, $route->getMethods());

        $route = $router->getRoutes()->get('my_method');
        $this->assertInstanceOf(Route::class, $route);

        $route = $router->head('/something/special', 'Something\Controller')
            ->setName('special')
            ->addMiddleware(TestMiddleware::class);

        $route = $router->getRoutes()->get('special');
        $this->assertInstanceOf(Route::class, $route);
        $this->assertCount(1, $route->getMiddlewares());
        $middlewares = $route->getMiddlewares();
        $this->assertEquals('Concrete\Tests\Core\Routing\TestMiddleware', $middlewares[0]->getMiddleware());

    }

    public function testRestrictions()
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $router->get('/rss/{identifier}', 'test')
            ->setName('rss')
            ->setRequirements(['identifier' => '[A-Za-z0-9_/.]+']);

        $router->post('/testing', '');
        $router->options('/something/else', '');

        $route = $router->getRoutes()->get('rss');
        $this->assertInstanceOf(Route::class, $route);

        $this->assertEquals('/rss/{identifier}/', $route->getPath());
        $methods = $route->getMethods();
        $this->assertCount(1, $methods);
        $this->assertEquals('GET', $methods[0]);
        $requirements = $route->getRequirements();
        $this->assertCount(1, $requirements);
        $this->assertEquals('[A-Za-z0-9_/.]+', $requirements['identifier']);
    }

    public function testCallbacks()
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $route = $router->get('/hello-world', function() { return 'hello world.'; })
            ->getRoute();
        $this->assertInstanceOf(Route::class, $route);
        $callback = $router->getAction($route);
        $this->assertInstanceOf(ClosureRouteAction::class, $callback);

        $route = $router->get('/hello-world', 'Concrete\Tests\Core\Routing\TestController')
            ->getRoute();
        $action = $router->getAction($route);
        $this->assertInstanceOf(ControllerRouteAction::class, $action);
        $action = $action->getAction();
        $this->assertEquals('Concrete\Tests\Core\Routing\TestController', $action);
    }

    public function testRouteMatchingAndControllerExecution()
    {
        $request = Request::create('http://www.awesome.com/something/hello-world?bar=1&foo=1');
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $router->post('/a-fun-test', 'Concrete\Tests\Core\Routing\TestController::test');
        $router->get('/something/hello-world', 'Concrete\Tests\Core\Routing\TestController::hello');
        $collection = $router->getRoutes();
        $context = new RequestContext();
        $context->fromRequest($request);

        $route = $router->matchRoute($request);
        $this->assertInstanceOf(MatchedRoute::class, $route);
        $route = $route->getRoute();
        $this->assertInstanceOf(Route::class, $route);
        $this->assertEquals('something_hello_world', $route->getName());
        $action = $router->getAction($route);
        $response = $action->execute($request, $route, []);
        $this->assertEquals('oh hai', $response->getContent());
    }

    public function testInvalidRoute()
    {
        $this->setExpectedException(ResourceNotFoundException::class);
        $request = Request::create('http://www.awesome.com/something/uh/oh/something_else');
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $route = $router->matchRoute($request);
    }

    public function testGrouping()
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $router->post('/a-fun-test', 'Concrete\Tests\Core\Routing\TestController::test');
        $router->buildGroup()
            ->setPrefix('/api/v1')
            ->routes(function($groupRouter) {
                $groupRouter->get('/hello-world', 'Concrete\Tests\Core\Routing\TestController::hello');
                $groupRouter->get('/status', 'Concrete\Tests\Core\Routing\TestController::status');
                $groupRouter->get('/user/{:user}', 'Concrete\Tests\Core\Routing\TestController::getUserDetails')
                    ->setName('user_details');
                return $groupRouter;
            });

        $routes = $router->getRoutes();
        $this->assertCount(4, $routes);

        // Test prefix and name.
        $this->assertEquals('/api/v1/hello-world/', $routes->get('api_v1_hello_world')->getPath());
        $this->assertEquals('/a-fun-test/', $routes->get('a_fun_test')->getPath());
        $this->assertEquals('/api/v1/status/', $routes->get('api_v1_status')->getPath());
        $this->assertEquals('/api/v1/user/{:user}/', $routes->get('user_details')->getPath());

        // Test everything
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $router->buildGroup()
            ->setPrefix('/ccm/system/user/')
            ->setNamespace('Concrete\Controller\Backend')
            ->setRequirements(['identifier' => '[A-Za-z0-9_/.]+'])
            ->addMiddleware('Concrete\Tests\Core\Routing\TestMiddleware')
            ->addMiddleware('Concrete\Tests\Core\Routing\AnotherMiddleware')
            ->routes(function($groupRouter) {
                $groupRouter->post('/add_group', 'User::addGroup');
                $groupRouter->post('/remove_group', 'User::removeGroup');
                $groupRouter->get('/get_json', 'User::getJSON');
                return $groupRouter;
            });

        $route = $router->getRoutes()->get('ccm_system_user_remove_group');
        $middlewares = $route->getMiddlewares();
        $this->assertCount(2, $middlewares);
        $this->assertEquals('Concrete\Tests\Core\Routing\AnotherMiddleware', $middlewares[1]->getMiddleware());
        $this->assertInstanceOf(Route::class, $route);
        $methods = $route->getMethods();
        $this->assertEquals('POST', $methods[0]);
        $requirements = $route->getRequirements();
        $this->assertCount(1, $requirements);
        $this->assertEquals('[A-Za-z0-9_/.]+', $requirements['identifier']);
        $action = $router->getAction($route);
        $this->assertInstanceOf(ControllerRouteAction::class, $action);
        $controller = $action->getAction();

        $this->assertEquals('Concrete\Controller\Backend\User::removeGroup', $controller);
    }

}
