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
     * The name of the CLI option that allows running CLI commands as root without confirmation.
     *
     * @var string
     */
    const ALLOWASROOT_OPTION = 'allow-as-root';

    /**
     * The name of the environment variable that allows running CLI commands as root without confirmation.
     *
     * @var string
     */
    const ALLOWASROOT_ENV = 'C5_CLI_ALLOW_AS_ROOT';

    /**
     * Can this command be executed as root?
     * If set to false, the command can be executed if one of these conditions is satisfied:
     * - the users is not root
     * - the --allow-as-root option is set
     * - the C5_CLI_ALLOW_AS_ROOT environment variable is set
     * - the console is interactive and the user explicitly confirm the operation.
     *
     * @var bool
     */
    protected $canRunAsRoot = true;

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
        if (!$this->canRunAsRoot && $this->isRunningAsRoot() === true) {
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
     * Allow/disallow running this command as root without confirmation.
     *
     * @param bool $canRunAsRoot if false the command can be executed if one of these conditions is satisfied:
     * - the users is not root
     * - the --allow-as-root option is set
     * - the C5_CLI_ALLOW_AS_ROOT environment variable is set
     * - the console is interactive and the user explicitly confirm the operation
     *
     * @return $this
     */
    protected function setCanRunAsRoot($canRunAsRoot)
    {
        $canRunAsRoot = (bool) $canRunAsRoot;
        if ($canRunAsRoot !== $this->canRunAsRoot) {
            if ($canRunAsRoot) {
                // Remove the --allow-as-root option
                $newOptions = [];
                foreach ($this->getDefinition()->getOptions() as $option) {
                    if ($option->getName() !== static::ALLOWASROOT_OPTION) {
                        $newOptions[] = $option;
                    }
                }
                $this->getDefinition()->setOptions($newOptions);
            } else {
                $this->addOption(static::ALLOWASROOT_OPTION, null, InputOption::VALUE_NONE, 'Allow executing this command as root without confirmation (you can also set the ' . static::ALLOWASROOT_ENV . ' environment variable)');
            }
            $this->canRunAsRoot = $canRunAsRoot;
        }

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
        if (!($input->hasOption(static::ALLOWASROOT_OPTION) && $input->getOption(static::ALLOWASROOT_OPTION)) && !@getenv(static::ALLOWASROOT_ENV)) {
            if (!$input->isInteractive()) {
                throw new UserMessageException("The current user is root: this is discouraged for this CLI command.\nYou can execute this command anyway by specifying the --" . static::ALLOWASROOT_OPTION . ' option or setting the ' . static::ALLOWASROOT_ENV . ' environment variable.');
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
