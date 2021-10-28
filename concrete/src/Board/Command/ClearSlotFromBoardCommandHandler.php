<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceSlotRule;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Doctrine\ORM\EntityManager;

class ClearSlotFromBoardCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerFactory
     */
    protected $loggerFactory;

    public function __construct(LoggerFactory $loggerFactory, EntityManager $entityManager)
    {
        $this->loggerFactory = $loggerFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(ClearSlotFromBoardCommand $command)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(InstanceSlotRule::class, 'r')
            ->where('r.slot = :slot')
            ->andWhere('r.instance = :instance');
        $qb->setParameter('slot', $command->getSlot());
        $qb->setParameter('instance', $command->getInstance());
        $qb->getQuery()->execute();

        $logger = $this->loggerFactory->createLogger(Channels::CHANNEL_BOARD);
        $logger->info(t('Slot {slot} in instance {instanceID} was cleared.'), [
            'slot' => $command->getSlot(),
            'instanceID' => $command->getInstance()->getBoardInstanceID(),
        ]);

    }


}
