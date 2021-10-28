<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\InstanceItemBatch;
use Doctrine\ORM\EntityManager;

class ClearBoardInstanceDataPoolCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ClearBoardInstanceDataPoolCommand $command)
    {
        $instance = $command->getInstance();
        foreach($instance->getItems() as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(InstanceItemBatch::class, 'ib')
            ->where('ib.instance = :instance');
        $queryBuilder->setParameter('instance', $instance);
        $queryBuilder->getQuery()->execute();

        $instance->setDateDataPoolLastUpdated(time());
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
    }


}
