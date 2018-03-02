<?php

namespace Concrete\Core\Console;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Environment\User;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Exception;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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
     * The name of the environment variable that allows running CLI commands as root without confirmation.
     *
     * @var string
     */
    const ENV_ALLOWASROOT = 'C5_ALLOW_CLI_AS_ROOT';

    /**
     * @var bool
     */
    protected $runAsRootDiscouraged = false;

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
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Console\Command\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($this->runAsRootDiscouraged && $this->isRunningAsRoot() === true && !@getenv(static::ENV_ALLOWASROOT)) {
            $this->confirmRunningAsRoot($input, $output);
        }
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
     * @return $this
     */
    protected function addEnvOption()
    {
        $this->addOption('env', null, InputOption::VALUE_REQUIRED, 'The environment (if not specified, we\'ll work with the configuration item valid for all environments)');

        return $this;
    }

    /**
     * Add the "--allow-as-root" option to the command options, and enable the runtime check to see if the current user is root.
     *
     * @return $this
     */
    protected function discourageRunAsRoot()
    {
        $this->addOption('allow-as-root', null, InputOption::VALUE_NONE, 'Allow executing this command as root without confirmation (you can also set the ' . static::ENV_ALLOWASROOT . ' environment variable)');
        $this->runAsRootDiscouraged = true;

        return $this;
    }

    /**
     * Is the current user root?
     *
     * @return bool|null NULL if unknown, or boolean if determined
     */
    protected function isRunningAsRoot()
    {
        $app = ApplicationFacade::getFacadeApplication();

        return $app->make(User::class)->isSuperUser();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws UserMessageException
     */
    protected function confirmRunningAsRoot(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('allow-as-root')) {
            if (!$input->isInteractive()) {
                throw new UserMessageException("The current user is root: this is discouraged for this CLI command.\nYou can execute this command anyway by specifying the --allow-as-root option or setting the " . static::ENV_ALLOWASROOT . " environment variable.");
            }
            $questionHelper = $this->getHelper('question');
            $question = new ConfirmationQuestion(
                "The current user is root: this is discouraged for this CLI command.\nDo you want to proceed anyway [Y/N]? ",
                false
            );
            if (!$questionHelper->ask($input, $output, $question)) {
                throw new UserMessageException('Operation aborted.');
            }
        }
    }
}
