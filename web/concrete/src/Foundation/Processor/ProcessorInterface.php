<?php
namespace Concrete\Core\Foundation\Processor;

interface ProcessorInterface
{
    public function __construct(TargetInterface $target);
    public function getTotalTasks();
    public function process();
    public function registerTask(TaskInterface $task, $priority = 0);
    public function getTasks();
    public function execute(ActionInterface $action);

}