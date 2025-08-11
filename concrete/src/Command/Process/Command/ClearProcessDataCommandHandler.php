<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Entity\Command\Process;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

class ClearProcessDataCommandHandler
{

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(LoggerFactory $loggerFactory, Connection $db)
    {
        $this->loggerFactory = $loggerFactory;
        $this->db = $db;
    }

    public function __invoke(ClearProcessDataCommand $command)
    {
        $logger = $this->loggerFactory->createLogger(Channels::CHANNEL_MESSENGER);
        $logger->notice(t('Clearing all process data...'));
        $this->db->executeStatement('delete from MessengerProcesses');
        $this->db->executeStatement('delete from MessengerTaskProcesses');
        $this->db->executeStatement('delete from MessengerBatches');
        $this->db->executeStatement('delete from MessengerMessages');
    }
}