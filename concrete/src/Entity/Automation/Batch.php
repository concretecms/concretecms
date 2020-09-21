<?php

namespace Concrete\Core\Entity\Automation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Batches")
 */
class Batch implements \JsonSerializable
{

    /**
     * @ORM\Id @ORM\Column(type="guid", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $totalProcesses;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $pendingProcesses;

        /**
         * @ORM\Column(type="integer", options={"unsigned":true})
         */
    protected $failedProcesses;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $completedProcesses;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
     */
    protected $dateCreated;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $dateCompleted;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getTotalProcesses()
    {
        return $this->totalProcesses;
    }

    /**
     * @param mixed $totalProcesses
     */
    public function setTotalProcesses($totalProcesses): void
    {
        $this->totalProcesses = $totalProcesses;
    }

    /**
     * @return mixed
     */
    public function getPendingProcesses()
    {
        return $this->pendingProcesses;
    }

    /**
     * @param mixed $pendingProcesses
     */
    public function setPendingProcesses($pendingProcesses): void
    {
        $this->pendingProcesses = $pendingProcesses;
    }

    /**
     * @return mixed
     */
    public function getFailedProcesses()
    {
        return $this->failedProcesses;
    }

    /**
     * @param mixed $failedProcesses
     */
    public function setFailedProcesses($failedProcesses): void
    {
        $this->failedProcesses = $failedProcesses;
    }

    /**
     * @return mixed
     */
    public function getCompletedProcesses()
    {
        return $this->completedProcesses;
    }

    /**
     * @param mixed $completedProcesses
     */
    public function setCompletedProcesses($completedProcesses): void
    {
        $this->completedProcesses = $completedProcesses;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
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

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->getID(),
            'task' => $this->getName(),
        ];
        return $data;
    }


}
