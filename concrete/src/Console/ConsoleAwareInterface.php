<?php

namespace Concrete\Core\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;
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
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     * @param \Symfony\Component\Console\Input\InputInterface|null $input
     * @return static Returns itself
     */
    public function setConsole(SymfonyApplication $console, OutputInterface $output = null, InputInterface $input = null);

    /**
     * Set the output object to use
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return static Returns itself
     */
    public function setOutput(OutputInterface $output);

    /**
     * Set the input object to use
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return static Returns itself
     */
    public function setInput(InputInterface $input);

}
