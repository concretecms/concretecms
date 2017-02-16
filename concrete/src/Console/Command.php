<?php
namespace Concrete\Core\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use Throwable;

abstract class Command extends SymfonyCommand
{
    /**
     * The return code we should return when an exception is thrown while running the command.
     *
     * @var int
     */
    const RETURN_CODE_ON_FAILURE = 1;

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        try {
            return parent::run($input, $output);
        } catch (Exception $x) {
            $error = $x;
        } catch (Throwable $x) {
            $error = $x;
        }
        $this->writeError($output, $error);

        return static::RETURN_CODE_ON_FAILURE;
    }

    /**
     * Write an exception.
     *
     * @param OutputInterface $output
     * @param Exception|Throwable $error
     */
    protected function writeError(OutputInterface $output, $error)
    {
        $message = trim($error->getMessage()) . "\n";
        if ($output->getVerbosity() >= $output::VERBOSITY_VERBOSE) {
            $file = $error->getFile();
            if ($file) {
                $message .= "\nFile:\n$file";
                if ($error->getLine()) {
                    $message .= ':' . $error->getLine();
                }
                $message .= "\n";
            }
            if ($output->getVerbosity() >= $output::VERBOSITY_VERY_VERBOSE) {
                $trace = $error->getTraceAsString();
                if ($trace) {
                    $message .= "\nTrace:\n$trace\n";
                }
            }
        }
        $output->writeln('<error>' . $message . '</error>');
    }

    /**
     * Add the "env" option to the command options.
     *
     * @return static
     */
    protected function addEnvOption()
    {
        $this->addOption('env', null, InputOption::VALUE_REQUIRED, 'The environment (if not specified, we\'ll work with the configuration item valid for all environments)');

        return $this;
    }
}
