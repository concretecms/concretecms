<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\Logger\LoggerFactory;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Doctrine\ORM\EntityManager;

class ClearBoardInstanceDataPoolCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(EntityManager $entityManager, LoggerFactory $loggerFactory)
    {
        $this->loggerFactory = $loggerFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ClearBoardInstanceDataPoolCommand $command)
    {
        $instance = $command->getInstance();
        $logger = $this->loggerFactory->createFromInstance($instance);

        $logger->write(t('Removing all items from instance data pool.'));

        foreach($instance->getItems() as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(InstanceItemBatch::class, 'ib')
            ->where('ib.instance = :instance');
        $queryBuilder->setParameter('instance', $instance);
        $queryBuilder->getQuery()->execute();

        $logger->write(t('Removing all instance item batches from instance.'));

        $instance->setDateDataPoolLastUpdated(time());
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
    }


}
