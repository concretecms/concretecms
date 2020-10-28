<?php
namespace Concrete\Core\Messenger\Registry;

use Concrete\Core\Application\Application;
use Concrete\Core\Messenger\HandlersLocator;
use Concrete\Core\Messenger\Transport\Sender\ConcreteDoctrineSendersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\DispatchAfterCurrentBusMiddleware;
use Symfony\Component\Messenger\Middleware\FailedMessageProcessingMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\RejectRedeliveredMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;

class AsyncBus implements RegistryInterface
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
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
                    new SendMessageMiddleware($this->app->make(ConcreteDoctrineSendersLocator::class)),
                    new HandleMessageMiddleware($this->app->make(HandlersLocator::class)),
                ]
            );
            return $bus;
        };
    }

}