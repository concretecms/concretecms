<?php
namespace Concrete\Core\Entity\Queue;

use Concrete\Core\Sharing\SocialNetwork\Service;
use Database;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="QueueBatches")
 */
class Batch
{

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $qbID;

    /**
     * The social service handle.
     *
     * @ORM\Column(type="string")
     */
    protected $batchHandle;


    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $total = 0;

    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $completed = 0;

    /**
     * @return mixed
     */
    public function getQueueBatchID()
    {
        return $this->qbID;
    }

    /**
     * @return mixed
     */
    public function getBatchHandle()
    {
        return $this->batchHandle;
    }

    /**
     * @param mixed $batchHandle
     */
    public function setBatchHandle($batchHandle): void
    {
        $this->batchHandle = $batchHandle;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param mixed $completed
     */
    public function setCompleted($completed): void
    {
        $this->completed = $completed;
    }




}
