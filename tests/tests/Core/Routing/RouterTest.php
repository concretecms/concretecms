<?php
namespace Concrete\Tests\Core\Routing;


use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\OAuthAuthenticationMiddleware;
use Concrete\Core\Http\Middleware\OAuthErrorMiddleware;
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
class TestController extends AbstractController
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

        $route = $router->getRoutes()->get('my_method_all');
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
        $callback = $router->resolveAction($route);
        $this->assertInstanceOf(ClosureRouteAction::class, $callback);

        $route = $router->get('/hello-world', 'Concrete\Tests\Core\Routing\TestController::hello')
            ->getRoute();
        $action = $router->resolveAction($route);
        $this->assertInstanceOf(ControllerRouteAction::class, $action);
        $this->assertEquals('Concrete\Tests\Core\Routing\TestController::hello', $action->getControllerCallback());
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
        $this->assertEquals('something_hello_world_get', $route->getName());
        $action = $router->resolveAction($route);
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
        $this->assertEquals('/api/v1/hello-world/', $routes->get('api_v1_hello_world_get')->getPath());
        $this->assertEquals('/a-fun-test/', $routes->get('a_fun_test_post')->getPath());
        $this->assertEquals('/api/v1/status/', $routes->get('api_v1_status_get')->getPath());
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

        $route = $router->getRoutes()->get('ccm_system_user_remove_group_post');
        $middlewares = $route->getMiddlewares();
        $this->assertCount(2, $middlewares);
        $this->assertEquals('Concrete\Tests\Core\Routing\AnotherMiddleware', $middlewares[1]->getMiddleware());
        $this->assertInstanceOf(Route::class, $route);
        $methods = $route->getMethods();
        $this->assertEquals('POST', $methods[0]);
        $requirements = $route->getRequirements();
        $this->assertCount(1, $requirements);
        $this->assertEquals('[A-Za-z0-9_/.]+', $requirements['identifier']);
        $action = $router->resolveAction($route);
        $this->assertInstanceOf(ControllerRouteAction::class, $action);
        $controller = $action->getControllerCallback();

        $this->assertEquals('Concrete\Controller\Backend\User::removeGroup', $controller);
    }

    public function testMultipleGroups()
    {
        $router = new Router(new RouteCollection(), new RouteActionFactory());
        $api = $router->buildGroup()
            ->setPrefix('/ccm/api/v1')
            ->scope('api')
            ->addMiddleware(OAuthErrorMiddleware::class)
            ->addMiddleware(OAuthAuthenticationMiddleware::class);

        $api->buildGroup()
            ->setPrefix('/system')
            ->routes(function($groupRouter) {
                $groupRouter->get('/info', 'Concrete\Tests\Core\Routing\TestController::hello');
                $groupRouter->get('/status', 'Concrete\Tests\Core\Routing\TestController::status');
                return $groupRouter;
            });

        $api->buildGroup()->scope('users')
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes(function($groupRouter) {
                $groupRouter->get('/users', 'Concrete\Tests\Core\Routing\TestController::hello');
                $groupRouter->post('/user/add', 'Concrete\Tests\Core\Routing\TestController::status');
                return $groupRouter;
            });
        $api->buildGroup()->scope('products')
            ->routes(function($groupRouter) {
                $groupRouter->get('/products', 'Concrete\Tests\Core\Routing\TestController::hello');
                $groupRouter->post('/products/add', 'Concrete\Tests\Core\Routing\TestController::status');
                return $groupRouter;
            });

        $routes = $router->getRoutes();
        $this->assertEquals(6, count($routes));
        $route = $router->getRoutes()->get('ccm_api_v1_system_status_get');
        $middlewares = $route->getMiddlewares();
        $scope = $route->getOption('oauth_scopes');

        $this->assertCount(2, $middlewares);
        $this->assertEquals('/ccm/api/v1/system/status/', $route->getPath());
        $this->assertEquals('api', $scope);

        $route = $router->getRoutes()->get('ccm_api_v1_products_add_post');
        $middlewares = $route->getMiddlewares();
        $scope = $route->getOption('oauth_scopes');
        $methods = $route->getMethods();

        $this->assertCount(2, $middlewares);
        $this->assertEquals('/ccm/api/v1/products/add/', $route->getPath());
        $this->assertCount(1, $methods);
        $this->assertEquals('POST', $methods[0]);
        $this->assertEquals('api,products', $scope);

        $route = $router->getRoutes()->get('ccm_api_v1_users_get');
        $scope = $route->getOption('oauth_scopes');
        $middlewares = $route->getMiddlewares();
        $this->assertCount(3, $middlewares);
        $this->assertEquals(FractalNegotiatorMiddleware::class, $middlewares[2]->getMiddleware());
        $this->assertEquals('api,users', $scope);

    }

}
