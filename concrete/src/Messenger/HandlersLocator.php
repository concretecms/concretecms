<?php
namespace Concrete\Core\Messenger;

use Closure;
use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class HandlersLocator implements HandlersLocatorInterface
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function getHandlers(Envelope $envelope): iterable
    {
        $handlerDescriptor = null;
        $message = $envelope->getMessage();
        $class = get_class($message);
        $handlersFromConfig = $this->config->get('app.command_handlers');
        if (isset($handlersFromConfig[$class])) {
            $handlerClass = $handlersFromConfig[$class];
        } else if ($message instanceof HandlerAwareCommandInterface) {
            $handlerClass = $message->getHandler();
        }
        if (isset($handlerClass)) {
            $builtClass = $this->app->build($handlerClass);
            if ($builtClass instanceof OutputAwareInterface) {
                $outputStamp = $envelope->last(OutputStamp::class);
                if ($outputStamp) {
                    /**
                     * @var $outputStamp OutputStamp
                     */
                    $builtClass->setOutput($outputStamp->getOutput());
                }
            }
            $callable = [$builtClass, '__invoke'];
            if (!is_callable($callable)) {
                throw new NoHandlerForMessageException(t('Unable to locate command handler for command: %s', $class));
            }

            $handlerDescriptor = new HandlerDescriptor($callable);
            if ($this->shouldHandle($envelope, $handlerDescriptor)) {
                yield $handlerDescriptor;
            }
        }

    }

    /**
     * Note: This was taken from the Symfony HandlersLocator class. I wish it were a trait or something more reusable
     * but the logic seems important so we should add it to our own.
     *
     * @param Envelope $envelope
     * @param HandlerDescriptor $handlerDescriptor
     * @return bool
     */
    private function shouldHandle(Envelope $envelope, HandlerDescriptor $handlerDescriptor): bool
    {
        if (null === $received = $envelope->last(ReceivedStamp::class)) {
            return true;
        }

        if (null === $expectedTransport = $handlerDescriptor->getOption('from_transport')) {
            return true;
        }

        return $received->getTransportName() === $expectedTransport;
    }


}
