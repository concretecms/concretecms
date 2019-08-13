<?php
namespace Concrete\Core\Foundation\Processor;

/**
 * @since 5.7.5.3
 */
interface ActionInterface
{
    public function __construct(ProcessorInterface $processor, TargetInterface $target, TaskInterface $task, $subject = null);
    public function execute();
    public function finish();
    public function getTarget();
    public function getSubject();
    public function getProcessor();
}
