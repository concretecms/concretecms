<?php

namespace Concrete\Core\Foundation\Queue\Batch;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Queue\Batch;;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Doctrine\ORM\EntityManager;

class BatchProgressUpdater
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * BatchProgressUpdater constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        // FYI - I know that the actual dependency here is the entity manager, not the application object. But
        // unfortunately, we can't pass the entity manager in here because this dependency is created
        // too early in the booting process, and it screws up the tests.
        $this->app = $app;
    }

    public function incrementTotals(Batch $batch, $additional)
    {
        $entityManager = $this->app->make(EntityManager::class);
        $total = $batch->getTotal();
        $total += $additional;
        $batch->setTotal($total);
        $entityManager->persist($batch);
        $entityManager->flush();
    }

    public function incrementCommandProgress(BatchableCommandInterface $command)
    {
        $entityManager = $this->app->make(EntityManager::class);
        $r = $entityManager->getRepository(Batch::class);
        $batch = $r->findOneByBatchHandle($command->getBatchHandle());
        if ($batch) {
            /**
             * @var $batch Batch
             */
            $completed = $batch->getCompleted();
            $completed++;
            $batch->setCompleted($completed);
            $entityManager->persist($batch);
            $entityManager->flush();
        }
    }


}