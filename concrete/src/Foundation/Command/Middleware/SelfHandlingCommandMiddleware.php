<?php

namespace Concrete\Core\Foundation\Command\Middleware;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Command\SelfHandlingCommandInterface;
use League\Tactician\Exception\CanNotInvokeHandlerException;
use League\Tactician\Middleware;

class SelfHandlingCommandMiddleware implements Middleware
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param object $command
     * @param callable $next
     * @return mixed
     * @throws CanNotInvokeHandlerException
     */
    public function execute($command, callable $next)
    {
        if (!$command instanceof SelfHandlingCommandInterface) {
            return $next($command);
        }
        if (!method_exists($command, 'handle')) {
            throw new CanNotInvokeHandlerException('Command does not have handle method');
        }
        return $this->app->call([$command, 'handle']);
    }
}
