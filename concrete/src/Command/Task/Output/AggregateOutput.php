<?php

namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\TaskInterface;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;

class AggregateOutput implements OutputInterface
{

    /**
     * @var OutputInterface[]
     */
    protected $outputs = [];

    /**
     * AggregateOutput constructor.
     * @param OutputInterface[] $outputs
     */
    public function __construct(array $outputs = [])
    {
        $this->outputs = $outputs;
    }

    public function addOutput(OutputInterface $output)
    {
        $this->outputs[] = $output;
    }

    public function write($message)
    {
        foreach($this->outputs as $output) {
            $output->write($message);
        }
    }

}
