<?php
namespace Concrete\Core\Command\Task\Output;

use Symfony\Component\Console\Output\OutputInterface as SymfonyConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput as SymfonyConsoleOutput;
defined('C5_EXECUTE') or die("Access Denied.");

class ConsoleOutput implements OutputInterface
{

    /**
     * @var SymfonyConsoleOutputInterface
     */
    protected $symfonyOutput;

    public function __construct(?SymfonyConsoleOutputInterface $symfonyOutput = null)
    {
        if ($symfonyOutput === null) {
            $symfonyOutput = new SymfonyConsoleOutput();
        }
        $this->symfonyOutput = $symfonyOutput;
    }

    public function write($message): void
    {
        $this->symfonyOutput->writeln($message);
    }

    public function writeError($message): void
    {
        if ($this->symfonyOutput->isVeryVerbose()) {
            throw new \Exception($message);
        } else {
            $this->symfonyOutput->writeln('<error>' . $message . '</error>');
        }
    }

}
