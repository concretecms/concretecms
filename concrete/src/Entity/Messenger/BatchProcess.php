<?php

namespace Concrete\Core\Entity\Messenger;

use Concrete\Core\Automation\Task\TaskInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="MessengerBatchProcesses")
 */
class BatchProcess extends Process
{

    /**
     * @ORM\Column(type="integer")
     */
    protected $totalJobs = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $pendingJobs = 0;

    /**
     * @ORM\Column(type="integer")
     */
    protected $failedJobs = 0;

    /**
     * @return mixed
     */
    public function getTotalJobs(): int
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
    public function getPendingJobs(): int
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

    public function getCompletedJobs(): int
    {
        return $this->getTotalJobs() - $this->getPendingJobs();
    }


}
