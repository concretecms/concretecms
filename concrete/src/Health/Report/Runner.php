<?php

namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Health\Report\AlertFinding;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Entity\Health\Report\InfoFinding;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SuccessFinding;
use Concrete\Core\Entity\Health\Report\WarningFinding;
use Concrete\Core\Health\Report\Finding\Details\ButtonDetails;
use Concrete\Core\Health\Report\Finding\Details\DetailsInterface;
use Concrete\Core\Health\Report\Finding\Details\LocationInterface;
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

    public function button(LocationInterface $location): ButtonDetails
    {
        return new ButtonDetails($location);
    }

    public function alert(
        string $message,
        DetailsInterface $details = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new AlertFinding(), $message, $details, $findingHandle);
    }

    public function warning(
        string $message,
        DetailsInterface $details = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new WarningFinding(), $message, $details, $findingHandle);
    }

    public function info(
        string $message,
        DetailsInterface $details = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new InfoFinding(), $message, $details, $findingHandle);
    }

    public function success(
        string $message,
        DetailsInterface $details = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new SuccessFinding(), $message, $details, $findingHandle);
    }

    /**
     * @param Finding $object
     * @param $message
     */
    public function finding(
        Finding $finding,
        string $message,
        DetailsInterface $details = null,
        string $findingHandle = null
    ): Finding {
        $finding->setMessage($message);
        $finding->setResult($this->getResult());
        $finding->setDetails($details);
        $finding->setHandle($findingHandle);
        $this->entityManager->persist($finding);
        $this->entityManager->flush();
        return $finding;
    }

}
