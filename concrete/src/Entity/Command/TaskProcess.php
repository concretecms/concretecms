<?php

namespace Concrete\Core\Entity\Command;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="processType", type="string")
 * @ORM\Table(name="MessengerTaskProcesses")
 */
class TaskProcess extends Process
{


    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $input;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\Automation\Task")
     */
    protected $task;

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
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task): void
    {
        $this->task = $task;
    }




}
