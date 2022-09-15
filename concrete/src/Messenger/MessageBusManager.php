<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Concrete\Core\Messenger\Transport\Sender\SendersLocator;

class MessageBusManager implements ContainerInterface
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $buses = [];

    /**
     * @var array
     */
    protected $customMiddleware = [];

    public function __construct(Application $app, Repository $config)
    {
        $this->config = $config;
        $this->app = $app;
    }

    /**
     * @param string $middlewareClass
     */
    public function addMiddleware(string $middlewareClass)
    {
        $this->customMiddleware[] = $middlewareClass;
        // Now let's zero out the buses we have so they get built again, just in case
        foreach($this->buses as $key => $bus) {
            unset($this->buses[$key]);
        }
    }

    public function registerBus(string $handle, \Closure $closure)
    {
        $this->buses[$handle] = $closure;
    }

    public function getBus(string $handle): ?MessageBusInterface
    {
        if (isset($this->buses[$handle])) {
            if ($this->buses[$handle] instanceof MessageBusInterface) {
                return $this->buses[$handle];
            }
            if ($this->buses[$handle] instanceof \Closure) {
                $bus = $this->buses[$handle]();
            }
        }

        if (!isset($bus)) {
            $bus = $this->buildBusFromConfig($handle);
        }

        if ($bus) {
            $this->buses[$handle] = $bus;
            return $bus;
        } else {
            throw new \RuntimeException(t('Unable to locate bus by handle: [%s]', $handle));
        }
    }

    private function buildBusFromConfig(string $handle): ?MessageBusInterface
    {
        $busConfig = $this->config->get('concrete.messenger.buses')[$handle] ?? null;
        if ($busConfig) {
            $customConfigMiddleware = $busConfig['middleware'];
            if ($busConfig['default_middleware']) {
                $middleware = [
                    new AddBusNameStampMiddleware($handle),
                    new RejectRedeliveredMessageMiddleware(),
                    new DispatchAfterCurrentBusMiddleware(),
                    new FailedMessageProcessingMiddleware()
                ];

                foreach ($customConfigMiddleware as $middlewareClass) {
                    $middleware[] = $this->app->make($middlewareClass);
                }

                foreach ($this->customMiddleware as $middlewareClass) {
                    $middleware[] = $this->app->make($middlewareClass);
                }

                $middleware[] = new SendMessageMiddleware($this->app->make(SendersLocator::class));
                $middleware[] = new HandleMessageMiddleware($this->app->make(HandlersLocator::class));
            } else {
                $middleware = $customConfigMiddleware; // I'm not really sure how you could _really_ use this through config but let's provide the option
            }
            $bus = new MessageBus($middleware);
            return $bus;
        }
        return null;
    }

    public function has($id)
    {
        return array_key_exists($id, $this->buses);
    }

    public function get($id)
    {
        return $this->getBus($id);
    }

}