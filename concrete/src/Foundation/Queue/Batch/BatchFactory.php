<?php

namespace Concrete\Core\Foundation\Queue\Batch;

use Concrete\Core\Entity\Queue\Batch;;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class BatchFactory
{

    /**
     * @var EntityManagerInterface
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

    public function createOrGetBatch(string $handle)
    {
        $batch = $this->getBatch($handle);
        if ($batch && $batch->getTotal() == $batch->getCompleted()) {
            // This batch is closed. Let's delete it and open a new one.
            $this->entityManager->remove($batch);
            $this->entityManager->flush();
            unset($batch);
        }
        if (!$batch) {
            $batch = new Batch();
            $batch->setBatchHandle($handle);
            $this->entityManager->persist($batch);
            $this->entityManager->flush();
        }
        return $batch;
    }

    public function getBatch(string $handle)
    {
        $r = $this->entityManager->getRepository(Batch::class);
        return $r->findOneByBatchHandle($handle);
    }

}