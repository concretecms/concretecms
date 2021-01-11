<?php
namespace Concrete\Core\Command\Task\Output;

use Symfony\Component\Console\Output\OutputInterface as SymfonyConsoleOutputInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ConsoleOutput implements OutputInterface
{

    /**
     * @var SymfonyConsoleOutputInterface
     */
    protected $symfonyOutput;

    public function __construct(SymfonyConsoleOutputInterface $symfonyOutput)
    {
        $this->symfonyOutput = $symfonyOutput;
    }

    public function write($message)
    {
        $this->symfonyOutput->writeln($message);
    }

}
