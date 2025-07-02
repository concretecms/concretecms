<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Board\DataSource\Driver\Manager as BoardDataSourceManager;
use Concrete\Core\Board\DataSource\Driver\NotifierAwareDriverInterface;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Doctrine\ORM\EntityManager;
use Illuminate\Config\Repository;

abstract class AbstractUpdateBoardInstanceCommandHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var BoardDataSourceManager
     */
    protected $boardDataSourceManager;

    public function __construct(
        Application $app,
        BoardDataSourceManager $boardDataSourceManager,
        EntityManager $entityManager,
        Repository $config
    )
    {
        $this->app = $app;
        $this->boardDataSourceManager = $boardDataSourceManager;
        $this->entityManager = $entityManager;
        $this->config = $config;
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_BOARD;
    }

    protected function performUpdate(): bool
    {
        return (bool) $this->config->get('concrete.boards.automatically_refresh_instances');
    }

    protected function getInstances(AbstractUpdateBoardInstanceCommand $command): array
    {
        $driver = $this->boardDataSourceManager->driver($command->getDriver());
        if ($driver instanceof NotifierAwareDriverInterface) {
            $notifier = $driver->getBoardInstanceNotifier();
            return $notifier->findBoardInstancesThatMayContainObject($command->getObject());
        }
    }
}
