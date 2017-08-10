<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Database\Connection\Timezone;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Package\Routine\AttachModeCompatibleRoutineInterface;
use Concrete\Core\Support\Facade\Application;
use Config;
use Database;
use DateTimeZone;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\ConnectionException;
use Exception;
use StartingPointPackage;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class InstallCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:install')
            ->setDescription('Install concrete5')
            ->addEnvOption()
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
            ->addOption('demo-username', null, InputOption::VALUE_REQUIRED, 'Additional user username', 'demo')
            ->addOption('demo-password', null, InputOption::VALUE_REQUIRED, 'Additional user password')
            ->addOption('demo-email', null, InputOption::VALUE_REQUIRED, 'Additional user email', 'demo@example.com')
            ->addOption('language', null, InputOption::VALUE_REQUIRED, 'The default concrete5 interface language (eg en_US)')
            ->addOption('site-locale', null, InputOption::VALUE_REQUIRED, 'The default site locale (eg en_US)')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'Use configuration file for installation')
            ->addOption('attach', null, InputOption::VALUE_NONE, 'Attach if database contains an existing concrete5 instance')
            ->addOption('force-attach', null, InputOption::VALUE_NONE, 'Always attach')
            ->addOption('interactive', 'i', InputOption::VALUE_NONE, 'Install using interactive (wizard) mode')
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
        if (file_exists(DIR_CONFIG_SITE . '/database.php')) {
            throw new Exception('concrete5 is already installed.');
        }
        if (isset($options['site-locale'])) {
            $locale = explode('_', $options['site-locale']);
            $_POST['siteLocaleLanguage'] = $locale[0];
            $_POST['siteLocaleCountry'] = $locale[1];
        } else {
            $_POST['siteLocaleLanguage'] = 'en';
            $_POST['siteLocaleCountry'] = 'US';
        }

        if (isset($options['timezone'])) {
            $_POST['SERVER_TIMEZONE'] = $options['timezone'];
        } else {
            $_POST['SERVER_TIMEZONE'] = @date_default_timezone_get() ?: 'UTC';
        }

        date_default_timezone_set($_POST['SERVER_TIMEZONE']);

        if (isset($options['language'])) {
            $_POST['locale'] = $options['language'];
        }

        Database::extend('install', function () use ($options) {
            return Database::getFactory()->createConnection([
                'host' => $options['db-server'],
                'user' => $options['db-username'],
                'password' => $options['db-password'],
                'database' => $options['db-database'],
            ]);
        });
        Database::setDefaultConnection('install');
        Config::set('database.connections.install', []);

        $cnt = $app->make(\Concrete\Controller\Install::class);

        $force_attach = $input->getOption('force-attach');
        $auto_attach = $force_attach || $input->getOption('attach');
        $cnt->setAutoAttach($auto_attach);

        $cnt->on_start();
        $fileWriteErrors = clone $cnt->fileWriteErrors;
        $e = $app->make('helper/validation/error');
        if (!$cnt->get('imageTest')) {
            $e->add('GD library must be enabled to install concrete5.');
        }
        if (!$cnt->get('mysqlTest')) {
            $e->add($cnt->getDBErrorMsg());
        }
        if (!$cnt->get('xmlTest')) {
            $e->add('SimpleXML and DOM must be enabled to install concrete5.');
        }
        if (!$cnt->get('phpVtest')) {
            $e->add('concrete5 requires PHP ' . $cnt->getMinimumPhpVersion() . ' or greater.');
        }
        if (is_object($fileWriteErrors)) {
            $e->add($fileWriteErrors);
        }
        $spl = StartingPointPackage::getClass($options['starting-point']);
        if ($spl === null) {
            $e->add('Invalid starting-point: ' . $options['starting-point']);
        }
        if (!$e->has()) {
            $_POST['DB_SERVER'] = $options['db-server'];
            $_POST['DB_USERNAME'] = $options['db-username'];
            $_POST['DB_PASSWORD'] = $options['db-password'];
            $_POST['DB_DATABASE'] = $options['db-database'];
            $_POST['SITE'] = $options['site'];
            $_POST['SAMPLE_CONTENT'] = $options['starting-point'];
            $_POST['uEmail'] = $options['admin-email'];
            $_POST['uPasswordConfirm'] = $_POST['uPassword'] = $options['admin-password'];
            if ($options['canonical-url']) {
                $_POST['canonicalUrlChecked'] = '1';
                $_POST['canonicalUrl'] = $options['canonical-url'];
            }
            if ($options['canonical-url-alternative']) {
                $_POST['canonicalUrlAlternativeChecked'] = '1';
                $_POST['canonicalUrlAlternative'] = $options['canonical-url-alternative'];
            }
            $e = $cnt->configure();
        }
        if ($e->has()) {
            throw new Exception(implode("\n", $e->getList()));
        }
        try {
            $attach_mode = $force_attach;

            if (!$force_attach && $cnt->isAutoAttachEnabled()) {
                /** @var Connection $db */
                $db = $app->make('database')->connection();

                if ($db->query('show tables')->rowCount()) {
                    $attach_mode = true;
                }
            }

            require DIR_CONFIG_SITE . '/site_install.php';
            require DIR_CONFIG_SITE . '/site_install_user.php';
            $routines = $spl->getInstallRoutines();
            foreach ($routines as $r) {
                // If we're
                if ($attach_mode && !$r instanceof AttachModeCompatibleRoutineInterface) {
                    $output->writeln("{$r->getProgress()}%: {$r->getText()} (Skipped)");
                    continue;
                }

                $output->writeln($r->getProgress() . '%: ' . $r->getText());
                $spl->executeInstallRoutine($r->getMethod());
            }
        } catch (Exception $ex) {
            $cnt->reset();
            throw $ex;
        }
        if (
            isset($options['demo-username']) && isset($options['demo-password']) && isset($options['demo-email'])
            &&
            is_string($options['demo-username']) && is_string($options['demo-password']) && is_string($options['demo-email'])
            &&
            ($options['demo-username'] !== '') && ($options['demo-password'] !== '') && ($options['demo-email'] !== '')
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
        // If we're in interactive mode, fire up the wizard
        if ($input->getOption('interactive')) {
            /** @var QuestionHelper $helper */
            $helper = $this->getHelper('question');

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

            $confirm = new Question('Would you like to install with these settings? [ y / n ]: ', false);
            $confirm->setValidator(function ($given) {
                if (!$given || !preg_match('/^[yn]/i', $given)) {
                    throw new \InvalidArgumentException(t('Please answer either Y or N.'));
                }
            });

            $answer = $helper->ask($input, $output, $confirm);

            // Cancel if they said no
            if (stripos('n', $answer) === 0) {
                $output->writeln('Installation cancelled.');
                exit;
            }

            // Add a bit of padding
            $output->writeln('');
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
            $default = null;
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
        if ($default) {
            return sprintf('%s? [Default: <options=bold>%s</>]: ', $option->getDescription(), $default);
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
                    $output->writeln(sprintf('<error>%s</error>', t('A database name is required.')));

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
                    $output->writeln(sprintf('<error>%s</error>', t('A time zone identifier is required.')));

                    return 'timezone';
                }
                try {
                    new DateTimeZone($timezone);
                } catch (Exception $x) {
                    $output->writeln(sprintf('<error>%s</error>', t('Invalid time zone identifier.')));

                    return 'timezone';
                }

                return true;
            },
            function (InputInterface $input, OutputInterface $output) {
                $params = [
                    'wrapperClass' => 'Concrete\Core\Database\Connection\Connection',
                    'dbname' => $input->getOption('db-database'),
                    'user' => $input->getOption('db-username'),
                    'password' => $input->getOption('db-password'),
                    'host' => $input->getOption('db-server'),
                    'driver' => 'pdo_mysql',
                ];

                $config = new Configuration();
                $connection = DriverManager::getConnection($params, $config);
                try {
                    $connection->connect();
                } catch (ConnectionException $e) {
                    $e = $e->getMessage();
                    $connection = false;
                }

                if (!$connection || !$connection->ping()) {
                    $output->writeln(sprintf('<error>%s</error>', t('Unable to connect using provided credentials.')));
                    if (isset($e)) {
                        $output->writeln(sprintf('<error>%s</error>', $e));
                    }

                    // Set the option to an empty string so that we don't output the password
                    $input->setOption('db-password', '');

                    return false;
                }
                $app = Application::getFacadeApplication();
                $ctz = $app->make(Timezone::class, ['connection' => $connection]);
                $deltaTimezone = $ctz->getDeltaTimezone($input->getOption('timezone'));
                if ($deltaTimezone !== null) {
                    $error = $ctz->describeDeltaTimezone($deltaTimezone);
                    $suggestTimezones = $ctz->getCompatibleTimezones();
                    if (!empty($suggestTimezones)) {
                        $suggestTimezones = array_keys($suggestTimezones);
                        sort($suggestTimezones);
                        $error .= "\n" . t('You may want to use one of these time zones:' . "\n" . implode("\n", $suggestTimezones));
                    }
                    $output->writeln(sprintf('<error>%s</error>', $error));

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
                    $output->writeln(sprintf('<error>%s</error>', t("The language code '%s' is not valid.", $code)));

                    return 'language';
                }

                return true;
            },
            [
                'site-locale',
                function (Question $question, InputInterface $input, InputOption $option) {
                    $newDefault = $input->getOption('language');
                    $input->setOption('site-locale', $newDefault);

                    return $question;
                },
            ],
            // Test the site locale
            function (InputInterface $input, OutputInterface $output) use ($checkLocale) {
                $code = $input->getOption('site-locale');
                if ($checkLocale($code) !== true) {
                    $output->writeln(sprintf('<error>%s</error>', t("The language code '%s' is not valid.", $code)));
                    
                    return 'site-locale';
                }
                
                return true;
            },
            ['config', 'none'],
        ];
    }
}
