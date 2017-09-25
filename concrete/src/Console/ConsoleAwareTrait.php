<?php

namespace Concrete\Core\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A trait that provides awareness of the console to objects
 */
trait ConsoleAwareTrait
{

    /** @var Application|null */
    protected $traitConsole;

    /** @var OutputInterface|null */
    protected $traitOutput;

    /** @var InputInterface|null */
    protected $traitInput;

    /**
     * Set the console object
     *
     * @param \Symfony\Component\Console\Application $console
     * @return void
     */
    public function setConsole(Application $console)
    {
        $this->traitConsole = $console;
    }

    /**
     * Set the output object to use
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    public function setOutput(OutputInterface $output)
    {
        $this->traitOutput = $output;
    }

    /**
     * Set the input object to use
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return void
     */
    public function setInput(InputInterface $input)
    {
        $this->traitInput = $input;
    }

    /**
     * Get an output object
     *
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->traitOutput ?: new NullOutput();
    }

    /**
     * Get an input object
     *
     * @return InputInterface
     */
    protected function getInput()
    {
        return $this->traitInput ?: new StringInput('');
    }

    /**
     * Find out if we are in console context
     * @return bool
     */
    protected function hasConsole()
    {
        return $this->traitConsole !== null;
    }

}
