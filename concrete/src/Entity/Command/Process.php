<?php

namespace Concrete\Core\Entity\Command;

use Concrete\Core\Command\Process\Logger\LoggerFactoryInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="ProcessRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="processType", type="string")
 * @ORM\Table(name="MessengerProcesses")
 */
class Process implements \JsonSerializable
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $dateStarted;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $dateCompleted;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $exitCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $exitMessage;

    /**
     * @ORM\OneToOne(targetEntity="Batch", cascade={"persist", "remove"})
     **/
    protected $batch;

    /**
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDateStarted()
    {
        return $this->dateStarted;
    }

    /**
     * @param mixed $dateStarted
     */
    public function setDateStarted($dateStarted): void
    {
        $this->dateStarted = $dateStarted;
    }

    /**
     * @return mixed
     */
    public function getDateCompleted()
    {
        return $this->dateCompleted;
    }

    /**
     * @param mixed $dateCompleted
     */
    public function setDateCompleted($dateCompleted): void
    {
        $this->dateCompleted = $dateCompleted;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param mixed $batch
     */
    public function setBatch($batch): void
    {
        $this->batch = $batch;
    }

    /**
     * @return mixed
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param mixed $exitCode
     */
    public function setExitCode($exitCode): void
    {
        $this->exitCode = $exitCode;
    }

    /**
     * @return mixed
     */
    public function getExitMessage()
    {
        return $this->exitMessage;
    }

    /**
     * @param mixed $exitMessage
     */
    public function setExitMessage($exitMessage): void
    {
        $this->exitMessage = $exitMessage;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $timezone = date_default_timezone_get();
        $dateStartedString = (new \DateTime('@' . $this->getDateStarted()))
            ->setTimezone(new \DateTimeZone($timezone))
            ->format('F d, Y g:i a');

        $dateCompletedString = null;
        if ($this->getDateCompleted()) {
            $dateCompletedString = (new \DateTime('@' . $this->getDateCompleted()))
                ->setTimezone(new \DateTimeZone($timezone))
                ->format('F d, Y g:i a');
        }

        $hasDetails = false;
        $logger = app(LoggerFactoryInterface::class)->createFromProcess($this);
        if ($logger) {
            $hasDetails = $logger->logExists();
        }

        $progress = 100;
        $batch = $this->getBatch();
        if ($batch) {
            $totalJobs = $batch->getTotalJobs();
            $pendingJobs = $batch->getPendingJobs();
            if ($totalJobs > 0) {
                $remaining = $totalJobs - $pendingJobs;
                $progress = round($remaining / $totalJobs, 2) * 100;
            }
        }
        $data = [
            'id' => $this->getID(),
            'name' => $this->getName(),
            'dateStarted' => $this->getDateStarted(),
            'dateCompleted' => $this->getDateCompleted(),
            'dateStartedString' => $dateStartedString,
            'dateCompletedString' => $dateCompletedString,
            'user' => $this->getUser(),
            'batch' => $batch,
            'hasDetails' => $hasDetails,
            'details' => [],
            'progress' => $progress
        ];
        return $data;
    }


}
