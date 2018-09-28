<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Cache\Cache;
use Concrete\Core\Console\Command;
use Concrete\Core\Install\ConnectionOptionsPreconditionInterface;
use Concrete\Core\Install\Installer;
use Concrete\Core\Install\PreconditionResult;
use Concrete\Core\Install\PreconditionService;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Package\Routine\AttachModeCompatibleRoutineInterface;
use Concrete\Core\Support\Facade\Application;
use Database;
use DateTimeZone;
use Exception;
use Hautelook\Phpass\PasswordHash;
use InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class InstallCommand extends Command
{
    /**
     * @var int
     * @access private
     */
    const OPTIONPRECONDITIONS_ERROR = 1;

    /**
     * @var int
     * @access private
     */
    const OPTIONPRECONDITIONS_WARNINGS = 2;

    /**
     * @var int
     * @access private
     */
    const OPTIONPRECONDITIONS_SUCCESS = 3;

    /**
     * @var bool|null
     */
    private $preconditionsPassed = null;

    /**
     * @var Installer|null
     */
    private $configuredInstaller = null;

    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:install')
            ->setDescription('Install concrete5')
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->addOption('db-server', null, InputOption::VALUE_REQUIRED, 'Location of database server')
            ->addOption('db-username', null, InputOption::VALUE_REQUIRED, 'Database username')
            ->addOption('db-password', null, InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db-database', null, InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('timezone', null, InputOption::VALUE_REQUIRED, 'The system time zone, compatible with the database one', @date_default_timezone_get() ?: 'UTC')
            ->addOption('site', null, InputOption::VALUE_REQUIRED, 'Name of the site', 'concrete5 Site')
            ->addOption('canonical-url', null, InputOption::VALUE_REQUIRED, 'Canonical URL', '')
            ->addOption('canonical-url-alternative', null, InputOption::VALUE_REQUIRED, 'Alternative canonical URL', '')
            ->addOption('starting-point', null, InputOption::VALUE_REQUIRED, 'Starting point to use', 'elemental_blank')
            ->addOption('admin-email', null, InputOption::VALUE_REQUIRED, 'Email of the admin user of the install', 'admin@example.com')
            ->addOption('admin-password', null, InputOption::VALUE_REQUIRED, 'Password of the admin user of the install')
            ->addOption('demo-username', null, InputOption::VALUE_REQUIRED, 'Additional user username')
            ->addOption('demo-password', null, InputOption::VALUE_REQUIRED, 'Additional user password')
            ->addOption('demo-email', null, InputOption::VALUE_REQUIRED, 'Additional user email', 'demo@example.com')
            ->addOption('language', null, InputOption::VALUE_REQUIRED, 'The default concrete5 interface language (eg en_US)')
            ->addOption('site-locale', null, InputOption::VALUE_REQUIRED, 'The default site locale (eg en_US)')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'Use configuration file for installation')
            ->addOption('attach', null, InputOption::VALUE_NONE, 'Attach if database contains an existing concrete5 instance')
            ->addOption('force-attach', null, InputOption::VALUE_NONE, 'Always attach')
            ->addOption('interactive', 'i', InputOption::VALUE_NONE, 'Install using interactive (wizard) mode')
            ->addOption('ignore-warnings', null, InputOption::VALUE_NONE, 'Ignore warnings')
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-install
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        if ($app->isInstalled()) {
            throw new Exception('concrete5 is already installed.');
        }
        if ($this->getPreconditionsPassed($app, $output) !== true) {
            throw new Exception('One or more precondition failed!');
        }
        $config = $app->make('config');
        $options = $this->getFinalOptions($input);
        $installer = $this->configuredInstaller;
        if ($installer === null) {
            $installer = $this->buildInstaller($options);
            switch ($this->checkOptionPreconditions($app, $installer, $input, $output)) {
                case self::OPTIONPRECONDITIONS_ERROR:
                    $output->writeln('One or more precondition failed!');
                    exit(1);
                case self::OPTIONPRECONDITIONS_WARNINGS:
                    if (!$input->getOption('ignore-warnings')) {
                        if (!$input->isInteractive()) {
                            $output->writeln('One or more precondition failed!');
                            exit(1);
                        }
                        $confirm = new Question('Configuration warnings detected. Would you like to install anyway? [Y]es / [N]o: ', false);
                        $confirm->setValidator(function ($given) {
                            if (!$given || !preg_match('/^[yn]/i', $given)) {
                                throw new InvalidArgumentException('Please answer either Y or N.');
                            }

                            return $given;
                        });
                        $helper = $this->getHelper('question');
                        $answer = $helper->ask($input, $output, $confirm);
                        // Cancel if they said no
                        if (stripos('n', $answer) === 0) {
                            $output->writeln('Installation cancelled.');
                            exit(1);
                        }
                    }
                    break;
            }
            $this->configuredInstaller = $installer;
        }
        Cache::disableAll();
        $spl = $installer->getStartingPoint(false);
        $installer->getOptions()->save();
        try {
            Database::extend('install', function () use ($options) {
                return Database::getFactory()->createConnection([
                    'host' => $options['db-server'],
                    'user' => $options['db-username'],
                    'password' => $options['db-password'],
                    'database' => $options['db-database'],
                ]);
            });
            Database::setDefaultConnection('install');
            $config->set('database.connections.install', []);
            $attach_mode = $options['force_attach'];
            if (!$attach_mode && $options['auto_attach']) {
                $db = $app->make('database')->connection();
                if ($db->query('show tables')->rowCount()) {
                    $attach_mode = true;
                }
            }
            $routines = $spl->getInstallRoutines();
            foreach ($routines as $r) {
                if ($attach_mode && !$r instanceof AttachModeCompatibleRoutineInterface) {
                    $output->writeln("{$r->getProgress()}%: {$r->getText()} (Skipped)");
                    continue;
                }
                $output->writeln($r->getProgress() . '%: ' . $r->getText());
                $spl->executeInstallRoutine($r->getMethod());
            }
        } catch (Exception $ex) {
            $installer->getOptions()->deleteFiles();
            throw $ex;
        }
        if (
            isset($options['demo-username']) && isset($options['demo-password']) && isset($options['demo-email'])
            &&
            ((string) $options['demo-username'] !== '') && ((string) $options['demo-password'] !== '') && ((string) $options['demo-email'] !== '')
        ) {
            $output->write('Adding demo user... ');
            \UserInfo::add([
                'uName' => $options['demo-username'],
                'uEmail' => $options['demo-email'],
                'uPassword' => $options['demo-password'],
            ])->getUserObject()->enterGroup(
                \Group::getByID(ADMIN_GROUP_ID)
            );
            $output->writeln('done.');
        }
        $output->writeln('<info>Installation Complete!</info>');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if ($this->getPreconditionsPassed(Application::getFacadeApplication(), $output) !== true) {
            throw new Exception('One or more precondition failed!');
        }
        // If we're in interactive mode, fire up the wizard
        if ($input->getOption('interactive')) {
            $app = Application::getFacadeApplication();
            $helper = $this->getHelper('question');
            /* @var \Symfony\Component\Console\Helper\QuestionHelper $helper */

            for (; ;) {
                // Get the wizard generator
                $wizard = $this->getWizard($input, $output);
                $hidden = [];

                // Loop over the questions
                foreach ($wizard as $key => $question) {
                    if ($question->isHidden()) {
                        // If this question is hidden, lets store its key for later
                        $hidden[] = $key;
                    }

                    // Set the option value to the result of asking the question
                    $input->setOption($key, $helper->ask($input, $output, $question));
                }

                $installer = $this->buildInstaller($this->getFinalOptions($input));
                switch ($this->checkOptionPreconditions($app, $installer, $input, $output)) {
                    case self::OPTIONPRECONDITIONS_ERROR:
                        $confirm = new Question('Errors detected! Would you like to change the above settings? [Y]es / [N]o: ', false);
                        $confirm->setValidator(function ($given) {
                            if (!$given || !preg_match('/^[yn]/i', $given)) {
                                throw new InvalidArgumentException('Please answer either Y, N or R.');
                            }

                            return $given;
                        });
                        $answer = $helper->ask($input, $output, $confirm);
                        // Cancel if they said no
                        if (stripos('n', $answer) === 0) {
                            $output->writeln('Installation cancelled.');
                            exit(1);
                        }
                        continue 2;
                    case self::OPTIONPRECONDITIONS_WARNINGS:
                        $confirm = new Question('Warnings detected! Would you like to change the above settings? [Y]es / [N]o / [A]bort: ', false);
                        $confirm->setValidator(function ($given) {
                            if (!$given || !preg_match('/^[yna]/i', $given)) {
                                throw new InvalidArgumentException('Please answer either Y, N or A.');
                            }

                            return $given;
                        });
                        $answer = $helper->ask($input, $output, $confirm);
                        // Cancel if they said no
                        if (stripos('a', $answer) === 0) {
                            $output->writeln('Installation cancelled.');
                            exit(1);
                        }
                        if (stripos('y', $answer) === 0) {
                            continue 2;
                        }
                        break;
                }

                // Lets output a table with the provided options for review
                $table = new Table($output);
                foreach ($this->getDefinition()->getOptions() as $option) {
                    if ($option->isValueRequired()) {
                        $name = $option->getName();
                        $value = $input->getOption($name) ?: '';

                        // If this question had hidden output, lets not show it now
                        if ($value && in_array($name, $hidden)) {
                            $value = '<options=bold>HIDDEN</>';
                        }

                        $table->addRow([$name, $value]);
                    }
                }

                $table->setHeaders(['Question', 'Value']);
                $table->render();

                $confirm = new Question('Would you like to install with these settings? [Y]es / [N]o / [E]dit: ', false);
                $confirm->setValidator(function ($given) {
                    if (!$given || !preg_match('/^[yne]/i', $given)) {
                        throw new InvalidArgumentException('Please answer either Y, N or R.');
                    }

                    return $given;
                });

                $answer = $helper->ask($input, $output, $confirm);

                // Cancel if they said no
                if (stripos('n', $answer) === 0) {
                    $output->writeln('Installation cancelled.');
                    exit(1);
                }

                // Retry if they ask so
                if (stripos('e', $answer) === 0) {
                    continue;
                }

                // Add a bit of padding
                $output->writeln('');
                $this->configuredInstaller = $installer;
                break;
            }
        }
    }

    /**
     * Do some procedural work to a row in the wizard step list to turn it into a proper question.
     *
     * @param $row
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Symfony\Component\Console\Question\Question
     */
    private function getQuestion($row, InputInterface $input)
    {
        $definition = $this->getDefinition();

        // Define default values
        $row = (array) $row;
        $default = null;
        $mutator = null;

        // Grab the key which is always first
        $key = array_shift($row);

        // If there's more stuff, this is probably the default value
        if ($row) {
            $default = array_shift($row);
        }

        // If the default value is callable, it's probably actually the mutator
        if (is_callable($default)) {
            $mutator = $default;
            $default = array_shift($row);
            if (is_callable($default)) {
                $default = $default($input);
            }
        } elseif ($row) { // Otherwise if there's still items, the mutator is last.
            $mutator = array_shift($row);
        }

        // If a value is provided already, use that as the default
        if ($provided = $input->getOption($key)) {
            $default = $provided;
        }

        // If we don't have a default, use the default from the InputOption
        $option = $definition->getOption($key);
        if (!$default) {
            $default = $option->getDefault();
        }

        // Create the new question deriving the question from the info we have
        $question = new Question($this->getQuestionString($option, $default), $default);

        // If we have a callable mutator, lets let it modify or replace the question
        if (is_callable($mutator)) {
            $question = $mutator($question, $input, $option);
        }

        return $question;
    }

    /**
     * A wizard generator.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param null $firstKey
     *
     * @return \Generator|\Symfony\Component\Console\Question\Question[]
     */
    private function getWizard(InputInterface $input, OutputInterface $output, $firstKey = null)
    {
        $questions = $this->wizardSteps();
        $tryAgain = false;
        $result = null;

        // Loop over the questions, parse them, then yield them out
        foreach ($questions as $question) {
            if (!$firstKey && $question instanceof \Closure) {
                $result = $question($input, $output, $this);

                if ($result === false || is_string($result)) {
                    $tryAgain = true;
                    break;
                }

                continue;
            }

            $question = (array) $question;
            if ($firstKey && $question[0] !== $firstKey) {
                continue;
            }

            // If we still have a firstKey set, that means we've hit the first key. Unset so that we don't test again
            if ($firstKey) {
                $firstKey = null;
            }

            if (in_array($question[0], ['demo-password', 'demo-email'], true) && '' === (string) $input->getOption('demo-username')) {
                continue;
            }

            yield $question[0] => $this->getQuestion($question, $input);
        }

        if ($tryAgain) {
            // Try again, passing the result as the first next item. This allows us to use this like a goto
            foreach ($this->getWizard($input, $output, $result) as $key => $value) {
                yield $key => $value;
            }
        }
    }

    /**
     * Take an option and return a question string.
     *
     * @param \Symfony\Component\Console\Input\InputOption $option
     * @param $default
     *
     * @return string
     */
    private function getQuestionString(InputOption $option, $default)
    {
        if ('' !== (string) $default) {
            if (stripos($option->getName(), 'password') !== false) {
                return sprintf('%s? [<options=bold>HIDDEN</>]: ', $option->getDescription(), $default);
            }

            return sprintf('%s? [<options=bold>%s</>]: ', $option->getDescription(), $default);
        }

        return sprintf('%s?: ', $option->getDescription());
    }

    /**
     * An array of steps
     * Items: [ "option-name", "default-value", function($question, $input, $option) : $question ].
     *
     * @return array
     */
    private function wizardSteps()
    {
        $checkLocale = function ($localeId) {
            $result = false;
            $chunks = \Punic\Data::explodeLocale($localeId);
            // Check that $localeId is well formatted
            if ($chunks !== null) {
                $normalizedLocaleId = ($chunks['territory'] === '') ? $chunks['language'] : "{$chunks['language']}_{$chunks['territory']}";
                // We don't support custom Scripts, check that the separator is "_", check language/territory lower/upper case
                if ($localeId === $normalizedLocaleId) {
                    // Check that the language ID is valid
                    if (\Punic\Language::getName($chunks['language']) !== $chunks['language']) {
                        // Check that the territory ID is valid (or absent)
                        if ($chunks['territory'] === '' || \Punic\Territory::getName($chunks['territory']) !== $chunks['territory']) {
                            $result = true;
                        }
                    }
                }
            }

            return $result;
        };

        return [
            ['db-server', '127.0.0.1'],
            'db-database',
            function (InputInterface $input, OutputInterface $output) {
                if (!trim($input->getOption('db-database'))) {
                    $output->writeln(sprintf('<error>%s</error>', 'A database name is required.'));

                    return 'db-database';
                }

                return true;
            },
            'db-username',
            [
                'db-password',
                function (Question $question, InputInterface $input) {
                    return $question->setHidden(true);
                },
            ],
            'timezone',
            function (InputInterface $input, OutputInterface $output) {
                $timezone = trim($input->getOption('timezone'));
                if ($timezone === '') {
                    $output->writeln(sprintf('<error>%s</error>', 'A time zone identifier is required.'));

                    return 'timezone';
                }
                try {
                    new DateTimeZone($timezone);
                } catch (Exception $x) {
                    $output->writeln(sprintf('<error>%s</error>', 'Invalid time zone identifier.'));

                    return 'timezone';
                }

                return true;
            },
            ['site', 'concrete5'],
            'canonical-url',
            'canonical-url-alternative',
            [
                'starting-point',
                'elemental_blank',
                function (Question $question, InputInterface $input) {
                    return new ChoiceQuestion($question->getQuestion(), ['elemental_blank', 'elemental_full'],
                        $question->getDefault());
                },
            ],
            'admin-email',
            [
                'admin-password',
                function (Question $question, InputInterface $input) {
                    return $question->setHidden(true);
                },
            ],
            // Test the password
            function (InputInterface $input, OutputInterface $output) {
                $answer = $input->getOption('admin-password');
                $error = new \ArrayObject();
                Application::getFacadeApplication()->make('validator/password')->isValid($answer, $error);

                if (count($error)) {
                    foreach ($error->getIterator() as $message) {
                        $output->writeln(sprintf('<error>%s</error>', $message));
                    }

                    // Set the option to an empty string so that we don't output the password
                    $input->setOption('admin-password', '');

                    return 'admin-password';
                }

                return true;
            },
            'demo-username',
            'demo-email',
            [
                'demo-password',
                function (Question $question, InputInterface $input) {
                    return $question->setHidden(true);
                },
            ],
            ['language', Localization::BASE_LOCALE],
            // Test the language
            function (InputInterface $input, OutputInterface $output) use ($checkLocale) {
                $code = $input->getOption('language');
                if ($checkLocale($code) !== true) {
                    $output->writeln(sprintf('<error>%s</error>', sprintf("The language code '%s' is not valid.", $code)));

                    return 'language';
                }

                return true;
            },
            [
                'site-locale',
                function (Question $question, InputInterface $input, InputOption $option) {
                    return $question;
                },
                function (InputInterface $input) {
                    return $input->getOption('site-locale') ?: $input->getOption('language') ?: Localization::BASE_LOCALE;
                },
            ],
            // Test the site locale
            function (InputInterface $input, OutputInterface $output) use ($checkLocale) {
                $code = $input->getOption('site-locale');
                if ($checkLocale($code) !== true) {
                    $output->writeln(sprintf('<error>%s</error>', sprintf("The language code '%s' is not valid.", $code)));

                    return 'site-locale';
                }

                return true;
            },
            ['config', 'none'],
        ];
    }

    private function getPreconditionsPassed(\Concrete\Core\Application\Application $app, OutputInterface $output)
    {
        if ($this->preconditionsPassed === null) {
            $this->preconditionsPassed = $this->checkPreconditions($app, $output);
        }

        return $this->preconditionsPassed;
    }

    /**
     * @param \Concrete\Core\Application\Application $app
     * @param OutputInterface $output
     *
     * @return bool
     */
    private function checkPreconditions(\Concrete\Core\Application\Application $app, OutputInterface $output)
    {
        $result = true;
        $service = $app->make(PreconditionService::class);
        /* @var PreconditionService $service */
        $requiredPreconditions = [];
        $optionalPreconditions = [];
        foreach ($service->getPreconditions(false) as $precondition) {
            if ($precondition->isOptional()) {
                $optionalPreconditions[] = $precondition;
            } else {
                $requiredPreconditions[] = $precondition;
            }
        }
        foreach ([
            'Checking required preconditions:' => $requiredPreconditions,
            'Checking optional preconditions:' => $optionalPreconditions,
        ] as $text => $preconditions) {
            if (empty($preconditions)) {
                continue;
            }
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln($text);
            }
            foreach ($preconditions as $precondition) {
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                    $output->write(sprintf('- %s... ', $precondition->getName()));
                } elseif ($precondition->isOptional()) {
                    continue;
                }
                $preconditionResult = $precondition->performCheck();
                switch ($preconditionResult->getState()) {
                    case PreconditionResult::STATE_PASSED:
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $message = $preconditionResult->getMessage();
                            $message = $message ? sprintf('passed (%s).', $message) : 'passed.';
                            $output->writeln(sprintf('<info>%s</info>', $message));
                        }
                        break;
                    case PreconditionResult::STATE_WARNING:
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(sprintf('<comment>%s</comment>', $preconditionResult->getMessage()));
                        }
                        break;
                    case PreconditionResult::STATE_SKIPPED:
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(sprintf('<comment>%s</comment>', $preconditionResult->getMessage() ?: 'skipped.'));
                        }
                        break;
                    case PreconditionResult::STATE_FAILED:
                    default:
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(sprintf('<error>%s</error>', $preconditionResult->getMessage()));
                        }
                        if (!$precondition->isOptional()) {
                            $result = false;
                        }
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * @param InputInterface $input
     *
     * @return array
     */
    private function getFinalOptions(InputInterface $input)
    {
        $options = $input->getOptions();
        if (isset($options['config']) && $options['config'] && strtolower($options['config']) !== 'none') {
            if (!is_file($options['config'])) {
                throw new Exception('Unable to find the configuration file ' . $options['config']);
            }
            $configOptions = include $options['config'];
            if (!is_array($configOptions)) {
                throw new Exception('The configuration file did not returned an array.');
            }
            foreach ($configOptions as $k => $v) {
                if (!$input->hasParameterOption("--$k")) {
                    $options[$k] = $v;
                }
            }
        }
        if (empty($options['timezone'])) {
            $options['timezone'] = @date_default_timezone_get() ?: 'UTC';
        }
        $options['attach'] = (bool) $input->getOption('attach');
        $options['force_attach'] = (bool) $input->getOption('force-attach');
        $options['auto_attach'] = $options['attach'] || $options['force_attach'];

        return $options;
    }

    /**
     * @param array $options
     *
     * @return Installer
     */
    private function buildInstaller(array $options)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $hasher = new PasswordHash($config->get('concrete.user.password.hash_cost_log2'), $config->get('concrete.user.password.hash_portable'));
        $installer = $app->make(Installer::class);
        $installer->getOptions()
            ->setConfiguration([
                'database' => [
                    'default-connection' => 'concrete',
                    'connections' => [
                        'concrete' => [
                            'driver' => 'c5_pdo_mysql',
                            'server' => $options['db-server'],
                            'database' => $options['db-database'],
                            'username' => $options['db-username'],
                            'password' => (string) $options['db-password'],
                            'charset' => 'utf8',
                        ],
                    ],
                ],
                'canonical-url' => $options['canonical-url'] ?: '',
                'canonical-url-alternative' => $options['canonical-url-alternative'] ?: '',
            ])
            ->setSiteLocaleId(isset($options['site-locale']) ? $options['site-locale'] : Localization::BASE_LOCALE)
            ->setUiLocaleId(isset($options['language']) ? $options['language'] : Localization::BASE_LOCALE)
            ->setAutoAttachEnabled($options['auto_attach'])
            ->setStartingPointHandle($options['starting-point'])
            ->setSiteName($options['site'])
            ->setUserEmail($options['admin-email'])
            ->setUserPasswordHash($hasher->HashPassword($options['admin-password']))
            ->setServerTimeZoneId($options['timezone'])
        ;

        return $installer;
    }

    /**
     * @param Installer $installer
     * @param \Concrete\Core\Application\Application $app
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int One of the InstallCommand::OPTIONPRECONDITIONS_... constants
     */
    private function checkOptionPreconditions(\Concrete\Core\Application\Application $app, Installer $installer, InputInterface $input, OutputInterface $output)
    {
        $connection = $installer->createConnection();
        $result = true;
        $someWarnings = false;
        $service = $app->make(PreconditionService::class);
        $requiredPreconditions = [];
        $optionalPreconditions = [];
        foreach ($service->getOptionsPreconditions() as $precondition) {
            if ($precondition->isOptional()) {
                $optionalPreconditions[] = $precondition;
            } else {
                $requiredPreconditions[] = $precondition;
            }
        }
        foreach ([
            'Checking required configuration preconditions:' => $requiredPreconditions,
            'Checking optional configuration preconditions:' => $optionalPreconditions,
        ] as $text => $preconditions) {
            if (empty($preconditions)) {
                continue;
            }
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $output->writeln($text);
            }
            foreach ($preconditions as $precondition) {
                if ($precondition instanceof ConnectionOptionsPreconditionInterface) {
                    $precondition->setConnection($connection);
                }
                $precondition->setInstallerOptions($installer->getOptions());
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                    $output->write(sprintf('- %s... ', $precondition->getName()));
                } elseif ($precondition->isOptional()) {
                    continue;
                }
                $preconditionResult = $precondition->performCheck();
                switch ($preconditionResult->getState()) {
                    case PreconditionResult::STATE_PASSED:
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $message = $preconditionResult->getMessage();
                            $message = $message ? sprintf('passed (%s).', $message) : 'passed.';
                            $output->writeln(sprintf('<info>%s</info>', $message));
                        }
                        break;
                    case PreconditionResult::STATE_WARNING:
                        $someWarnings = true;
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(sprintf('<comment>%s</comment>', $preconditionResult->getMessage()));
                        }
                        break;
                    case PreconditionResult::STATE_SKIPPED:
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(sprintf('<comment>%s</comment>', $preconditionResult->getMessage() ?: 'skipped.'));
                        }
                        break;
                    case PreconditionResult::STATE_FAILED:
                    default:
                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                            $output->writeln(sprintf('<error>%s</error>', $preconditionResult->getMessage()));
                        }
                        if (!$precondition->isOptional()) {
                            $result = false;
                        } else {
                            $someWarnings = true;
                        }
                        break;
                }
            }
        }

        if ($result === false) {
            return self::OPTIONPRECONDITIONS_ERROR;
        } elseif ($someWarnings === true) {
            return self::OPTIONPRECONDITIONS_WARNINGS;
        } else {
            return self::OPTIONPRECONDITIONS_SUCCESS;
        }
    }
}
