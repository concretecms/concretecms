<?php

namespace Concrete\Core\Console;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Foundation\Environment\User;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Exception;
use LogicException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Throwable;

/**
 * base command class
 * Large swaths of this class have been copied from illuminate/config 5.2 and 5.5
 * so you may refer to their documentation for some things.
 */
abstract class Command extends SymfonyCommand
{
    /**
     * @deprecated Use SUCCESS
     *
     * @var int
     */
    public const RETURN_CODE_ON_SUCCESS = self::SUCCESS;

    /**
     * @deprecated Use FAILURE
     *
     * @var int
     */
    public const RETURN_CODE_ON_FAILURE = self::FAILURE;

    /**
     * Concrete requires symfony/console ^5.2, and the INVALID constant has been introduced in symfony/console 5.3.0
     *
     * @var int
     */
    public const INVALID = 2;

    /**
     * The name of the CLI option that allows running CLI commands as root without confirmation.
     *
     * @var string
     */
    public const ALLOWASROOT_OPTION = 'allow-as-root';

    /**
     * The name of the environment variable that allows running CLI commands as root without confirmation.
     *
     * @var string
     */
    public const ALLOWASROOT_ENV = 'C5_CLI_ALLOW_AS_ROOT';

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var \Concrete\Core\Console\OutputStyle
     */
    protected $output;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * The command signature.
     *
     * @see https://laravel.com/docs/5.5/artisan#defining-input-expectations
     * ex: `config:set {item} {value} {--quiet}`
     *
     * Argument: `{item}`
     * Argument array: `{item*}`
     * Optional argument: `{item?}`
     * Optional with default: `{item=foo}`
     * Argument with description: `{item : The config "item" to set}`
     *
     * Option: `{--quiet}`
     * Option with value: `{--ignore=}`
     * Option array: `{--ignore=*}`
     * Option with default: `{--ignore=default}`
     * Short option: `{--Q|quiet}`
     * Option with description: `{--ignore=default : The item to ignore}`
     *
     * @var string
     */
    protected $signature;

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

    public function __construct($name = null)
    {
        if ($this->signature) {
            $this->configureUsingFluentDefinition();
        } else {
            parent::__construct($this->name ?: $name);
        }

        // Once we have constructed the command, we'll set the description and other
        // related properties of the command. If a signature wasn't used to build
        // the command we'll set the arguments and the options on this command.
        if ((string) $this->description !== '') {
            $this->setDescription($this->description);
        }
        $this->setHidden($this->hidden);
        if (!isset($this->signature)) {
            $this->specifyParameters();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        // Store the input and output
        $this->input = $input;
        $this->output = new OutputStyle($input, $output);

        // Run the command
        return parent::run($this->input, $this->output);
    }

    /**
     * Call another console command.
     *
     * @param string $command
     * @param array $arguments
     *
     * @return int
     */
    public function call($command, array $arguments = [])
    {
        $arguments['command'] = $command;

        return $this->getApplication()->find($command)->run(
            new ArrayInput($arguments),
            $this->output
        );
    }

    /**
     * Call another console command silently.
     *
     * @param string $command
     * @param array $arguments
     *
     * @return int
     */
    public function callSilent($command, array $arguments = [])
    {
        $arguments['command'] = $command;

        return $this->getApplication()->find($command)->run(
            new ArrayInput($arguments),
            new NullOutput()
        );
    }

    /**
     * Determine if the given argument is present.
     *
     * @param string|int $name
     *
     * @return bool
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Get the value of a command argument.
     *
     * @param string|null $key
     *
     * @return string|array
     */
    public function argument($key = null)
    {
        if ($key === null) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

    /**
     * Get all of the arguments passed to the command.
     *
     * @return array
     */
    public function arguments()
    {
        return $this->argument();
    }

    /**
     * Determine if the given option is present.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Get the value of a command option.
     *
     * @param string $key
     *
     * @return string|array
     */
    public function option($key = null)
    {
        if ($key === null) {
            return $this->input->getOptions();
        }

        return $this->input->getOption($key);
    }

    /**
     * Get all of the options passed to the command.
     *
     * @return array
     */
    public function options()
    {
        return $this->option();
    }

    /**
     * Confirm a question with the user.
     *
     * @param string $question
     * @param bool $default
     *
     * @return bool
     */
    public function confirm($question, $default = false)
    {
        return $this->output->confirm($question, $default);
    }

    /**
     * Prompt the user for input.
     *
     * @param string $question
     * @param string $default
     *
     * @return string
     */
    public function ask($question, $default = null)
    {
        return $this->output->ask($question, $default);
    }

    /**
     * Prompt the user for input with auto completion.
     *
     * @param string $question
     * @param array $choices
     * @param string $default
     * @param null $attempts
     * @param null $strict
     *
     * @return string
     */
    public function askWithCompletion($question, array $choices, $default = null, $attempts = null, $strict = null)
    {
        return $this->output->askWithCompletion($question, $choices, $default, $attempts, $strict);
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * @param string $question
     * @param bool $fallback
     *
     * @return string
     */
    public function secret($question, $fallback = true)
    {
        return $this->output->secret($question, $fallback);
    }

    /**
     * Give the user a single choice from an array of answers.
     *
     * @param string $question
     * @param array $choices
     * @param string $default
     * @param mixed $attempts
     * @param bool $multiple
     *
     * @return string
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {
        return $this->output->choice($question, $choices, $default, $attempts, $multiple);
    }

    /**
     * Format input to textual table.
     *
     * @param array $headers
     * @param \Illuminate\Contracts\Support\Arrayable|array $rows
     * @param string $tableStyle
     * @param array $columnStyles
     *
     * @return void
     */
    public function table(array $headers, array $rows, $tableStyle = 'default', array $columnStyles = [])
    {
        $this->output->table($headers, $rows, $tableStyle, $columnStyles);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Symfony\Component\Console\Application|\Concrete\Core\Console\Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }

    /**
     * Configure the console command using a fluent definition.
     *
     * @return void
     */
    protected function configureUsingFluentDefinition()
    {
        list($name, $arguments, $options) = Parser::parse($this->signature);
        parent::__construct($this->name = $name);
        // After parsing the signature we will spin through the arguments and options
        // and set them on this command. These will already be changed into proper
        // instances of these "InputArgument" and "InputOption" Symfony classes.
        foreach ($arguments as $argument) {
            $this->getDefinition()->addArgument($argument);
        }
        foreach ($options as $option) {
            $this->getDefinition()->addOption($option);
        }
    }

    /**
     * Specify the arguments and options on the command.
     *
     * @return void
     */
    protected function specifyParameters()
    {
        // We will loop through all of the arguments and options for the command and
        // set them all on the base command instance. This specifies what can get
        // passed into these commands as "parameters" to control the execution.
        foreach ($this->getArguments() as $arguments) {
            call_user_func_array([$this, 'addArgument'], $arguments);
        }
        foreach ($this->getOptions() as $options) {
            call_user_func_array([$this, 'addOption'], $options);
        }
    }

    /**
     * Get the arguments for this command.
     *
     * If $this->signature is specified, this method has no effect.
     *
     * @return array [[$name, $mode = null, $description = '', $default = null], ...]
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the options for this command.
     *
     * If $this->signature is specified, this method has no effect.
     *
     * @return array [[$name, $shortcut = null, $mode = null, $description = '', $default = null], ...]
     */
    protected function getOptions()
    {
        return [];
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
     *
     * @deprecated Use $this->output to manage your output
     * @see OutputStyle::error()
     */
    protected function writeError(OutputInterface $output, $error)
    {
        $result = [trim($error->getMessage())];

        // If the output is verbose, add file and location
        if ($output->isVerbose()) {
            $file = $error->getFile();
            if ($file) {
                $result[] = "File: {$file}" . ($error->getLine() ? ':' . $error->getLine() : '');
            }
        }

        // If the output is very verbose, add stacktrace
        if ($output->isVeryVerbose()) {
            $trace = $error->getTraceAsString();
            $result[] = 'Trace:';
            $result[] = $trace;
        }

        $this->output->error($result);
    }

    /**
     * Add the "env" option to the command options.
     *
     * @return Command
     */
    protected function addEnvOption()
    {
        $this->addOption(
            'env',
            null,
            InputOption::VALUE_REQUIRED,
            'The environment (if not specified, we\'ll work with the configuration item valid for all environments)'
        );

        return $this;
    }

    /**
     * Allow/disallow running this command as root without confirmation.
     *
     * @param bool $canRunAsRoot if false the command can be executed if one of these conditions is satisfied:
     *                           - the users is not root
     *                           - the --allow-as-root option is set
     *                           - the C5_CLI_ALLOW_AS_ROOT environment variable is set
     *                           - the console is interactive and the user explicitly confirm the operation
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

    /**
     * This method is overridden to pipe execution to the handle method hiding input and output.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!method_exists($this, 'handle')) {
            throw new LogicException('You must define the public handle() method in the command implementation.');
        }
        $result = $this->getApplication()->getConcrete()->call([$this, 'handle']);
        switch (gettype($result)) {
            case 'integer':
                return $result;
            case 'boolean':
                return $result ? static::SUCCESS : static::FAILURE;
            case 'double':
                return (int) $result;
            case 'string':
                return is_numeric($result) ? (int) $result : static::SUCCESS;
            default:
                return static::SUCCESS;
        }
    }
}
