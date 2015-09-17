<?php
namespace Concrete\Core\Foundation\Processor;

interface TaskInterface
{

    public function execute(ActionInterface $action);
    public function finish(ActionInterface $action);

}
