<?php
namespace Concrete\Core\Foundation\Processor;

interface ActionInterface
{

    public function __construct(TargetInterface $target, TaskInterface $task, $subject = null);
    public function execute();
    public function finish();

}
