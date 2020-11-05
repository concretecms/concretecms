<?php
namespace Concrete\Core\Messenger\Registry;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class CommandBus implements RegistryInterface
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function getSendersLocator(): SendersLocatorInterface
    {
        return $this->app->make(SendersLocator::class);
    }

    public function getBusBuilder(string $handle): callable
    {
        return function() use ($handle) {
            $bus = new MessageBus(
                [
                    new AddBusNameStampMiddleware($handle),
                    new RejectRedeliveredMessageMiddleware(),
                    new DispatchAfterCurrentBusMiddleware(),
                    new FailedMessageProcessingMiddleware(),
                    new SendMessageMiddleware($this->getSendersLocator()),
                    new HandleMessageMiddleware($this->app->make(HandlersLocator::class)),
                ]
            );
            return $bus;
        };
    }
}