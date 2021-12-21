<?php
namespace Concrete\Core\Entity\Automation;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="TaskSetTasks"
 * )
 */
class TaskSetTask
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Automation\Task", inversedBy="set_tasks")
     */
    protected $task;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="\Concrete\Core\Entity\Automation\TaskSet", inversedBy="tasks")
     */
    protected $set;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    protected $displayOrder = 0;

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
    public function setTask($task)
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getTaskSet()
    {
        return $this->set;
    }

    /**
     * @param mixed $set
     */
    public function setTaskSet($set)
    {
        $this->set = $set;
    }

    /**
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * @param mixed $displayOrder
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;
    }
}
