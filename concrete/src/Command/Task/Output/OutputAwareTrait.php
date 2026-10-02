<?php

namespace Concrete\Core\Command\Task\Output;

/**
 * A trait used with OutputAwareInterface
 */
trait OutputAwareTrait
{

    /**
     * The output to write to. It's null only until setOutput() is called (that is, only in the constructor of the classes using this trait).
     *
     * @var OutputInterface
     */
    protected $output;

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

}
