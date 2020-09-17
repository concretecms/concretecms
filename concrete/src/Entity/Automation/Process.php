<?php

namespace Concrete\Core\Entity\Automation;

use Concrete\Core\Automation\Task\Controller\ControllerInterface;
use Concrete\Core\Automation\Task\Manager;
use Concrete\Core\Automation\Task\TaskInterface;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Processes")
 */
class Process implements \JsonSerializable
{

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $started;

    /**
     * @ORM\ManyToOne(targetEntity="Task")
     **/
    protected $task;

    /**
     * @var ???
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
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param mixed $started
     */
    public function setStarted($started): void
    {
        $this->started = $started;
    }

    /**
     * @return mixed
     */
    public function getTask(): TaskInterface
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

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->getID(),
            'task' => $this->task,
        ];
        return $data;
    }


}
