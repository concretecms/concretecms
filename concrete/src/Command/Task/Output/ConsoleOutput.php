<?php
namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\TaskInterface;
use Symfony\Component\Console\Output\Output as SymfonyConsoleOutput;

defined('C5_EXECUTE') or die("Access Denied.");

class ConsoleOutput implements OutputInterface
{

    /**
     * @var SymfonyConsoleOutput
     */
    protected $symfonyOutput;

    public function __construct(SymfonyConsoleOutput $symfonyOutput)
    {
        $this->symfonyOutput = $symfonyOutput;
    }

    public function write($message)
    {
        $this->symfonyOutput->writeln($message);
    }

}
