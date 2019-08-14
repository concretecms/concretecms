<?php
namespace Concrete\Core\Foundation\Processor;

/**
 * @since 5.7.5
 */
interface TaskInterface
{
    public function execute(ActionInterface $action);
    /**
     * @since 5.7.5.3
     */
    public function finish(ActionInterface $action);
}
