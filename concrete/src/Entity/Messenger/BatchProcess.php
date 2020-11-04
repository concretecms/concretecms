<?php

namespace Concrete\Core\Entity\Messenger;

use Concrete\Core\Automation\Task\TaskInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="MessengerBatchProcesses")
 */
class BatchProcess implements \JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $totalJobs = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $pendingJobs = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $failedJobs = 0;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $completedJobs = 0;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $input;

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
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param mixed $input
     */
    public function setInput($input): void
    {
        $this->input = $input;
    }

    /**
     * @return mixed
     */
    public function getTotalJobs()
    {
        return $this->totalJobs;
    }

    /**
     * @param mixed $totalJobs
     */
    public function setTotalJobs($totalJobs): void
    {
        $this->totalJobs = $totalJobs;
    }

    /**
     * @return mixed
     */
    public function getPendingJobs()
    {
        return $this->pendingJobs;
    }

    /**
     * @param mixed $pendingJobs
     */
    public function setPendingJobs($pendingJobs): void
    {
        $this->pendingJobs = $pendingJobs;
    }

    /**
     * @return mixed
     */
    public function getFailedJobs()
    {
        return $this->failedJobs;
    }

    /**
     * @param mixed $failedJobs
     */
    public function setFailedJobs($failedJobs): void
    {
        $this->failedJobs = $failedJobs;
    }

    /**
     * @return mixed
     */
    public function getCompletedJobs()
    {
        return $this->completedJobs;
    }

    /**
     * @param mixed $completedJobs
     */
    public function setCompletedJobs($completedJobs): void
    {
        $this->completedJobs = $completedJobs;
    }

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->getID(),
        ];
        return $data;
    }


}
