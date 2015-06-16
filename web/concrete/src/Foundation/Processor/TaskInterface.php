<?php
namespace Concrete\Core\Foundation\Processor;

interface TaskInterface
{

    public function execute(TargetInterface $target, $subject);

}
