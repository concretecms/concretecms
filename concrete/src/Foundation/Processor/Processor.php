<?php
namespace Concrete\Core\Foundation\Processor;

class Processor implements ProcessorInterface
{
    protected $target;
    protected $tasks = array();

    public function __construct(TargetInterface $target)
    {
        $this->target = $target;
    }

    public function registerTask(TaskInterface $task, $priority = 0)
    {
        $this->tasks[] = array($priority, $task);
    }

    public function execute(ActionInterface $action)
    {
        $action->execute();
    }

    public function finish()
    {
        $tasks = $this->getTasks();
        foreach ($tasks as $task) {
            $action = new Action($this, $this->target, $task[1]);
            $action->finish();
        }
    }

    public function getTasks()
    {
        $tasks = $this->tasks;
        usort($tasks, function ($a, $b) {
            if ($a[0] == $b[0]) {
                return 0;
            }

            return ($a[0] < $b[0]) ? -1 : 1;
        });

        return $tasks;
    }

    public function getTotalTasks()
    {
        return count($this->getTasks());
    }

    public function process()
    {
        $tasks = $this->getTasks();
        foreach ($this->target->getItems() as $targetItem) {
            foreach ($tasks as $task) {
                $action = new Action($this, $this->target, $task[1], $targetItem);
                $this->execute($action);
            }
        }
        $this->finish();
    }
}
