<?php

namespace Concrete\Core\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface to flag an object as being aware of the symfony console components.
 *
 * Be aware that these objects are not guaranteed to be provided
 */
interface ConsoleAwareInterface
{

    /**
     * Set the console object
     *
     * @param \Symfony\Component\Console\Application $console
     * @return void
     */
    public function setConsole(Application $console);

    /**
     * Set the output object to use
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    public function setOutput(OutputInterface $output);

    /**
     * Set the input object to use
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return void
     */
    public function setInput(InputInterface $input);

}
