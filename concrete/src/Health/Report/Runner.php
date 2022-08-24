<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Health\Report\AlertFinding;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Entity\Health\Report\InfoFinding;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SuccessFinding;
use Concrete\Core\Entity\Health\Report\WarningFinding;
use Doctrine\ORM\EntityManager;

class Runner
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Result
     */
    protected $result;

    public function __construct(EntityManager $entityManager, Result $result)
    {
        $this->entityManager = $entityManager;
        $this->result = $result;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    /**
     * @param Result $result
     */
    public function setResult(Result $result): void
    {
        $this->result = $result;
    }

    public function alert($message): Finding
    {
        return $this->finding(new AlertFinding(), $message);
    }

    public function warning($message): Finding
    {
        return $this->finding(new WarningFinding(), $message);
    }

    public function info($message): Finding
    {
        return $this->finding(new InfoFinding(), $message);
    }

    public function success($message): Finding
    {
        return $this->finding(new SuccessFinding(), $message);
    }

    /**
     * @param Finding $object
     * @param $message
     */
    public function finding(Finding $finding, $message): Finding
    {
        $finding->setMessage($message);
        $finding->setResult($this->getResult());
        $this->entityManager->persist($finding);
        $this->entityManager->flush();
        return $finding;
    }

}
