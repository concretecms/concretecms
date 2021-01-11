<?php

namespace Concrete\Core\Command\Task\Command;

use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Command\Task\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecuteConsoleTaskCommand extends Command
{

    /**
     * @var TaskInterface
     */
    protected $task;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * ExecuteConsoleTaskCommand constructor.
     * @param TaskInterface $task
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(TaskInterface $task, InputInterface $input, OutputInterface $output)
    {
        $this->task = $task;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @return TaskInterface
     */
    public function getTask(): TaskInterface
    {
        return $this->task;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }




}