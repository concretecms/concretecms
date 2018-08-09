<?php
namespace Concrete\Core\Foundation\Processor;

class Action implements ActionInterface
{
    public function __construct(ProcessorInterface $processor, TargetInterface $target, TaskInterface $task, $subject = null)
    {
        $this->processor = $processor;
        $this->subject = $subject;
        $this->target = $target;
        $this->task = $task;
    }

    public function getProcessor()
    {
        return $this->processor;
    }

    public function setProcessor($processor)
    {
        $this->processor = $processor;
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
        $this->task->execute($this);
    }

    public function finish()
    {
        $this->task->finish($this);
    }
}
