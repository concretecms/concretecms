<?php

namespace Concrete\Core\Foundation\Queue\Batch;

use Concrete\Core\Entity\Queue\Batch;;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Doctrine\ORM\EntityManager;

class BatchProgressUpdater
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param Driver     $driver
     * @param Serializer $serializer
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function incrementTotals(Batch $batch, $additional)
    {
        $total = $batch->getTotal();
        $total += $additional;
        $batch->setTotal($total);
        $this->entityManager->persist($batch);
        $this->entityManager->flush();
    }

    public function incrementCommandProgress(BatchableCommandInterface $command)
    {
        $r = $this->entityManager->getRepository(Batch::class);
        $batch = $r->findOneByBatchHandle($command->getBatchHandle());
        if ($batch) {
            /**
             * @var $batch Batch
             */
            $completed = $batch->getCompleted();
            $completed++;
            $batch->setCompleted($completed);
            $this->entityManager->persist($batch);
            $this->entityManager->flush();
        }
    }


}