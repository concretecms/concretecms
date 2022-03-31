<?php

namespace Concrete\Core\Entity\Command;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\EntityListeners({"\Concrete\Core\Command\Batch\Listener"})
 * @ORM\Table(name="MessengerBatches")
 */
class Batch implements \JsonSerializable
{

    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'totalJobs' => $this->getTotalJobs(),
            'pendingJobs' => $this->getPendingJobs(),
            'failedJobs' => $this->getFailedJobs(),
        ];
    }


}
