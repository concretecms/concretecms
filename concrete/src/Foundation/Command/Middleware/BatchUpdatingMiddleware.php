<?php
namespace Concrete\Core\Foundation\Command\Middleware;

use Concrete\Core\Entity\Queue\Batch;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use Doctrine\ORM\EntityManager;
use League\Tactician\Middleware;

class BatchUpdatingMiddleware implements Middleware
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute($command, callable $next)
    {

        $return = $next($command);


        if ($command instanceof BatchableCommandInterface) {
            $r = $this->entityManager->getRepository(Batch::class);
            $batch = $r->findOneByBatchHandle($command->getBatchHandle());
            if ($batch) {
                /**
                 * @var $batch Batch
                 */
                $total = $batch->getTotal();
                $completed = $batch->getCompleted();

                $completed++;

                if ($completed < $total) {
                    $batch->setCompleted($completed);
                    $this->entityManager->persist($batch);
                } else {
                    $this->entityManager->remove($batch);
                }
                $this->entityManager->flush();
            }
        }


        return $return;

    }
}