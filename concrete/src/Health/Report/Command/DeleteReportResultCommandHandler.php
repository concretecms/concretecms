<?php
namespace Concrete\Core\Health\Report\Command;


use Concrete\Core\Entity\Health\Report\Result;
use Doctrine\ORM\EntityManager;

class DeleteReportResultCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function __invoke(DeleteReportResultCommand $command)
    {
        $result = $this->entityManager->find(Result::class, $command->getResultId());
        if ($result) {
            $this->entityManager->remove($result);
            $this->entityManager->flush();
        }
    }

}