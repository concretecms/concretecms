<?php
namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\Output\OutputInterface;

/**
 * This interface declares awareness of task output.
 */
interface OutputAwareInterface
{

    public function setOutput(OutputInterface $output);

}
