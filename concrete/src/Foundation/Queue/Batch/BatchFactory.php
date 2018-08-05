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

    public function getBatchFromCommand(string $command)
    {
        $reflectionClass = new \ReflectionClass($command);
        if (!$reflectionClass->implementsInterface(BatchableCommandInterface::class)) {
            throw new \Exception(t('Command %s passed to getBatchFromCommand does not implement the BatchableCommandInterface', $command));
        }

        $handle = $command::getBatchHandle();
        return $this->getBatch($handle);
    }

    public function incrementTotals(Batch $batch, $additional)
    {
        $total = $batch->getTotal();
        $total += $additional;
        $batch->setTotal($total);
        $this->entityManager->persist($batch);
        $this->entityManager->flush();
    }

    public function getBatch(string $handle)
    {
        $r = $this->entityManager->getRepository(Batch::class);
        $batch = $r->findOneByBatchHandle($handle);
        if (!$batch) {
            $batch = new Batch();
            $batch->setBatchHandle($handle);
            $this->entityManager->persist($batch);
            $this->entityManager->flush();
        }
        return $batch;
    }

}