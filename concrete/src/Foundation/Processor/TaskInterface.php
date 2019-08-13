<?php
namespace Concrete\Core\Foundation\Processor;

/**
 * @since 5.7.5
 */
interface TaskInterface
{
    public function execute(ActionInterface $action);
    public function finish(ActionInterface $action);
}
