<?php

namespace Concrete\Tests\Routing;

use Concrete\Core\Http\Request;
use Concrete\Core\Routing\ClosureRouteAction;
use Concrete\Core\Routing\Route;
use Concrete\Core\Routing\RouteActionFactory;
use Concrete\Core\Routing\RouteActionInterface;
use Mockery as M;

class RouteActionFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testRouteActionPassesThrough()
    {
        $fakeAction = M::mock(RouteActionInterface::class);

        $route = M::mock(Route::class);
        $route->shouldReceive('getAction')->andReturn($fakeAction);

        $factory = new RouteActionFactory();
        $this->assertEquals($fakeAction, $factory->createAction($route));
    }

    public function testClosureRouteAction()
    {
        $called = 0;
        $fakeAction = function() use (&$called) {
            $called++;
        };

        $route = M::mock(Route::class);
        $route->shouldReceive('getAction')->andReturn($fakeAction);

        // Build the new action
        $factory = new RouteActionFactory();
        $result = $factory->createAction($route);
        $this->assertInstanceOf(ClosureRouteAction::class, $result);

        $this->assertEquals(0, $called, 'The route action was called sooner than expected.');

        // run the action and make sure the underlying closure is called.
        $result->execute(M::mock(Request::class), $route, []);
        $this->assertEquals(1, $called, 'The route action was not called.');
    }

}
