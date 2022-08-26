<?php
namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Health\Report\AlertFinding;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Entity\Health\Report\InfoFinding;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SuccessFinding;
use Concrete\Core\Entity\Health\Report\WarningFinding;
use Concrete\Core\Health\Report\Finding\SettingsLocation\SettingsLocationInterface;
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

    public function alert(string $message, SettingsLocationInterface $location = null): Finding
    {
        return $this->finding(new AlertFinding(), $message, $location);
    }

    public function warning(string $message, SettingsLocationInterface $location = null): Finding
    {
        return $this->finding(new WarningFinding(), $message, $location);
    }

    public function info(string $message, SettingsLocationInterface $location = null): Finding
    {
        return $this->finding(new InfoFinding(), $message, $location);
    }

    public function success(string $message, SettingsLocationInterface $location = null): Finding
    {
        return $this->finding(new SuccessFinding(), $message, $location);
    }

    /**
     * @param Finding $object
     * @param $message
     */
    public function finding(Finding $finding, string $message, SettingsLocationInterface $location = null): Finding
    {
        $finding->setMessage($message);
        $finding->setResult($this->getResult());
        $finding->setSettingsLocation($location);
        $this->entityManager->persist($finding);
        $this->entityManager->flush();
        return $finding;
    }

}
