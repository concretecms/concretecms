<?php
namespace Concrete\Core\Foundation\Bus;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Bus\Command\CommandInterface;
use Concrete\Core\Foundation\Bus\Handler\MethodNameInflector\HandleClassNameWithFallbackInflector;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\QueueMiddleware;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;

class Bus
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var InMemoryLocator
     */
    protected $locator;

    /**
     * @var CommandBus
     */
    protected $syncBus;

    /**
     * @var CommandBus
     */
    protected $queueBus;

    /**
     * Bus constructor.
     * @param Application $app
     * @param Repository $config
     */
    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->locator = new InMemoryLocator();
        foreach($this->config->get('app.commands') as $command => $handler) {
            $this->locator->addHandler($app->make($handler), $command);
        }
    }

    /**
     * @return InMemoryLocator
     */
    public function getCommandLocator()
    {
        return $this->locator;
    }

    public function executeCommand(CommandInterface $command)
    {
        return $this->getSyncBus()->handle($command);
    }

    public function queueCommand(QueueableCommand $command)
    {
        return $this->getQueueBus()->handle($command);
    }

    /**
     * @var CommandBus
     */
    public function getSyncBus()
    {
        if (!isset($this->syncBus)) {
            $handlerMiddleware = new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $this->getCommandLocator(),
                new HandleClassNameWithFallbackInflector()
            );
            $this->syncBus = new CommandBus([$handlerMiddleware]);
        }
        return $this->syncBus;
    }

    /**
     * @var CommandBus
     */
    public function getQueueBus()
    {
        if (!isset($this->queueBus)) {
            $handlerMiddleware = new QueueMiddleware(
                $this->app->make('queue/producer')
            );
            $this->queueBus = new CommandBus([$handlerMiddleware]);
        }
        return $this->queueBus;
    }
}