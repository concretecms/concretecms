<?php

namespace Concrete\Tests\Core\Http\Middleware;

use Concrete\Core\Http\Exception\ForbiddenException;
use Concrete\Core\Http\Exception\RedirectException;
use Concrete\Core\Http\Exception\UserFacingException;
use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\HttpExceptionMiddleware;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactoryInterface;

class HttpExceptionMiddlewareTest extends \PHPUnit_Framework_TestCase
{

    public function testRedirectException()
    {
        // The args we expect to see passed through
        $args = ['http://some.url/', 1337, ['test=true']];

        // The middleware frame that throws the exception
        $frame = $this->getMiddlewareFrame(function() use ($args) {
            throw new RedirectException($args[0], $args[1], $args[2]);
        });

        // Response Factory
        $factory = $this->getMock(ResponseFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('redirect')
            ->willReturnCallback(function() {
                return func_get_args();
            });

        // Request
        $request = $this->getMock(Request::class);

        // Run the middleware
        $middleware = new HttpExceptionMiddleware($factory);
        $result = $middleware->process($request, $frame);

        // Make sure we have the same array values.
        $this->assertSame($args, $result);
    }

    public function testForbiddenException()
    {

        // The args we expect to see passed through
        $args = ['http://request.url/', 403, ['test=true']];

        // The middleware frame that throws the exception
        $frame = $this->getMiddlewareFrame(function() use ($args) {
            throw new ForbiddenException($args[0], $args[2]);
        });

        // Response Factory
        $factory = $this->getMock(ResponseFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('forbidden')
            ->willReturnCallback(function() {
                return func_get_args();
            });

        // Request
        $request = $this->getMock(Request::class);
        $request
            ->method('getRequestUri')
            ->willReturn('http://request.url/');

        // Run the middleware
        $middleware = new HttpExceptionMiddleware($factory);
        $result = $middleware->process($request, $frame);

        // Make sure we have the same array values.
        $this->assertSame($args, $result);
    }

    public function testUserFacingException()
    {

        // The args we expect to see passed through
        $args = ['ERROR!!!!', 'Title', 1337, ['test=true']];
        $expected = [ (object) ['content' => $args[0], 'title' => $args[1] ], $args[2], $args[3]];

        // The middleware frame that throws the exception
        $frame = $this->getMiddlewareFrame(function() use ($args) {
            throw new UserFacingException($args[0], $args[1], $args[2], $args[3]);
        });

        // Response Factory
        $factory = $this->getMock(ResponseFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('error')
            ->willReturnCallback(function() {
                return func_get_args();
            });

        // Request
        $request = $this->getMock(Request::class);

        // Run the middleware
        $middleware = new HttpExceptionMiddleware($factory);
        $result = $middleware->process($request, $frame);

        // Make sure we have the same array values.
        $this->assertEquals($expected, $result);
    }


    /**
     * @param callable $callback
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMiddlewareFrame(callable $callback)
    {
        $frame = $this->getMockForAbstractClass(DelegateInterface::class);
        $frame
            ->expects($this->once())
            ->method('next')
            ->willReturnCallback($callback);

        return $frame;
    }

}
