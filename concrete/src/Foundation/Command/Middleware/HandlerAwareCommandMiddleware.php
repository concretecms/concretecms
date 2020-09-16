<?php

namespace Concrete\Core\Foundation\Command\Middleware;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;
use Concrete\Core\Foundation\Command\SelfHandlingCommandInterface;
use League\Tactician\Exception\CanNotInvokeHandlerException;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use League\Tactician\Middleware;

class HandlerAwareCommandMiddleware implements Middleware
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var MethodNameInflector
     */
    protected $methodNameInflector;

    public function __construct(Application $app, MethodNameInflector $methodNameInflector)
    {
        $this->app = $app;
        $this->methodNameInflector = $methodNameInflector;
    }

    /**
     * @param object $command
     * @param callable $next
     * @return mixed
     * @throws CanNotInvokeHandlerException
     */
    public function execute($command, callable $next)
    {

        if (!$command instanceof HandlerAwareCommandInterface) {
            return $next($command);
        }

        $handler = $this->app->make($command->getHandler());
        $methodName = $this->methodNameInflector->inflect($command, $handler);

        // is_callable is used here instead of method_exists, as method_exists
        // will fail when given a handler that relies on __call.
        if (!is_callable([$handler, $methodName])) {
            throw CanNotInvokeHandlerException::forCommand(
                $command,
                "Method '{$methodName}' does not exist on handler"
            );
        }

        return $handler->{$methodName}($command);
    }
}
