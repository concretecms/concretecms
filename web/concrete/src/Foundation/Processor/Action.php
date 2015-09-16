<?php
namespace Concrete\Core\Foundation\Processor;

class Action implements ActionInterface
{

    public function __construct(TargetInterface $target, TaskInterface $task, $subject = null)
    {
        $this->subject = $subject;
        $this->target = $target;
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return TargetInterface
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param TargetInterface $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param TaskInterface $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }

    public function execute()
    {
        $this->task->execute($this->getTarget(), $this->subject);
    }

    public function finish()
    {
        $this->task->finish($this->getTarget());
    }

}
