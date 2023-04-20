<?php
namespace Concrete\Core\Health\Report\Message;

use Concrete\Core\Entity\Health\Report\Result;
use Doctrine\ORM\EntityManager;

class FinishReportMessageHandler
{


    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(FinishReportMessage $message)
    {
        $result = $this->entityManager->find(Result::class, $message->getResultId());
        $result->setDateCompleted(time());
        $this->entityManager->persist($result);
        $this->entityManager->flush();
    }

}