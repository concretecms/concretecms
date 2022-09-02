<?php

namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Health\Report\AlertFinding;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Entity\Health\Report\InfoFinding;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SuccessFinding;
use Concrete\Core\Entity\Health\Report\WarningFinding;
use Concrete\Core\Health\Report\Finding\Controls\ButtonControls;
use Concrete\Core\Health\Report\Finding\Controls\ControlsInterface;
use Concrete\Core\Health\Report\Finding\Controls\LocationInterface;
use Concrete\Core\Health\Report\Finding\Message\Message;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
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

    public function button(LocationInterface $location): ButtonControls
    {
        return new ButtonControls($location);
    }

    public function alert(
        $message,
        ControlsInterface $controls = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new AlertFinding(), $message, $controls, $findingHandle);
    }

    public function warning(
        $message,
        ControlsInterface $controls = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new WarningFinding(), $message, $controls, $findingHandle);
    }

    public function info(
        $message,
        ControlsInterface $controls = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new InfoFinding(), $message, $controls, $findingHandle);
    }

    public function success(
        $message,
        ControlsInterface $controls = null,
        string $findingHandle = null
    ): Finding {
        return $this->finding(new SuccessFinding(), $message, $controls, $findingHandle);
    }

    /**
     * @param Finding $object
     * @param $message
     */
    public function finding(
        Finding $finding,
        $message,
        ControlsInterface $controls = null,
        string $findingHandle = null
    ): Finding {
        if (!($message instanceof MessageInterface)) {
            $message = new Message($message);
        }
        $finding->setMessage($message);
        $finding->setResult($this->getResult());
        $finding->setControls($controls);
        $finding->setHandle($findingHandle);
        $this->entityManager->persist($finding);
        $this->entityManager->flush();
        return $finding;
    }

}
