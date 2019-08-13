<?php
namespace Concrete\Core\Foundation\Processor;

/**
 * @since 5.7.5.3
 */
interface ProcessorInterface
{
    public function getTotalTasks();
    public function process();
    public function registerTask(TaskInterface $task, $priority = 0);
    public function getTasks();
    public function execute(ActionInterface $action);
}
