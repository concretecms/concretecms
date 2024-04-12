<?php

namespace Concrete\Core\Health\Report;

use Concrete\Core\Entity\Health\Report\AlertFinding;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Entity\Health\Report\InfoFinding;
use Concrete\Core\Entity\Health\Report\Result;
use Concrete\Core\Entity\Health\Report\SuccessFinding;
use Concrete\Core\Entity\Health\Report\WarningFinding;
use Concrete\Core\Health\Report\Finding\Control\ButtonControl;
use Concrete\Core\Health\Report\Finding\Control\ControlInterface;
use Concrete\Core\Health\Report\Finding\Control\LocationInterface;
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

    public function button(LocationInterface $location): ButtonControl
    {
        return new ButtonControl($location);
    }

    public function alert(
        $message,
        ?ControlInterface $control = null,
        ?string $findingHandle = null
    ): Finding {
        return $this->finding(new AlertFinding(), $message, $control, $findingHandle);
    }

    public function warning(
        $message,
        ?ControlInterface $control = null,
        ?string $findingHandle = null
    ): Finding {
        return $this->finding(new WarningFinding(), $message, $control, $findingHandle);
    }

    public function info(
        $message,
        ?ControlInterface $control = null,
        ?string $findingHandle = null
    ): Finding {
        return $this->finding(new InfoFinding(), $message, $control, $findingHandle);
    }

    public function success(
        $message,
        ?ControlInterface $control = null,
        ?string $findingHandle = null
    ): Finding {
        return $this->finding(new SuccessFinding(), $message, $control, $findingHandle);
    }

    /**
     * @param Finding $object
     * @param $message
     */
    public function finding(
        Finding $finding,
        $message,
        ?ControlInterface $control = null,
        ?string $findingHandle = null
    ): Finding {
        if (!($message instanceof MessageInterface)) {
            $message = new Message($message);
        }
        $finding->setMessage($message);
        $finding->setResult($this->getResult());
        $finding->setControl($control);
        $finding->setHandle($findingHandle);
        $this->entityManager->persist($finding);
        $this->entityManager->flush();
        return $finding;
    }

}
