<?php
namespace Concrete\Core\Foundation\Processor;

interface ActionInterface
{

    public function __construct(TargetInterface $target, TaskInterface $task, $subject);
    public function execute();
}
