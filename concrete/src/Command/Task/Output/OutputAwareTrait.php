<?php

namespace Concrete\Core\Command\Task\Output;

/**
 * A trait used with OutputAwareInterface
 */
trait OutputAwareTrait
{

    /**
     * @var OutputInterface
     */
    protected $output;

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

}
