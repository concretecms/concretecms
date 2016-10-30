<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Package\Routine\AttachModeCompatibleRoutineInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use Database;
use Config;
use StartingPointPackage;
use Concrete\Core\Support\Facade\Application;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:install')
            ->setDescription('Install concrete5')
            ->addOption('db-server', null, InputOption::VALUE_REQUIRED, 'Location of database server')
            ->addOption('db-username', null, InputOption::VALUE_REQUIRED, 'Database username')
            ->addOption('db-password', null, InputOption::VALUE_REQUIRED, 'Database password')
            ->addOption('db-database', null, InputOption::VALUE_REQUIRED, 'Database name')
            ->addOption('site', null, InputOption::VALUE_REQUIRED, 'Name of the site', 'concrete5 Site')
            ->addOption('canonical-url', null, InputOption::VALUE_REQUIRED, 'Canonical URL', '')
            ->addOption('canonical-ssl-url', null, InputOption::VALUE_REQUIRED, 'Canonical URL over SSL', '')
            ->addOption('starting-point', null, InputOption::VALUE_REQUIRED, 'Starting point to use', 'elemental_blank')
            ->addOption('admin-email', null, InputOption::VALUE_REQUIRED, 'Email of the admin user of the install', 'admin@example.com')
            ->addOption('admin-password', null, InputOption::VALUE_REQUIRED, 'Password of the admin user of the install')
            ->addOption('demo-username', null, InputOption::VALUE_REQUIRED, 'Additional user username', 'demo')
            ->addOption('demo-password', null, InputOption::VALUE_REQUIRED, 'Additional user password')
            ->addOption('demo-email', null, InputOption::VALUE_REQUIRED, 'Additional user email', 'demo@example.com')
            ->addOption('locale', null, InputOption::VALUE_REQUIRED, 'The default site locale (eg en_US)')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'Use configuration file for installation')
            ->addOption('attach', null, InputOption::VALUE_NONE, 'Attach if database contains an existing concrete5 instance')
            ->addOption('force-attach', null, InputOption::VALUE_NONE, 'Always attach')
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  1 errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-install
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        try {
            $app = Application::getFacadeApplication();
            $options = $input->getOptions();
            if (isset($options['config'])) {
                if (!is_file($options['config'])) {
                    throw new Exception('Unable to find the configuration file '.$options['config']);
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
            if (file_exists(DIR_CONFIG_SITE.'/database.php')) {
                throw new Exception('concrete5 is already installed.');
            }
            if (isset($options['locale'])) {
                $locale = explode('_', $options['locale']);
                $_POST['siteLocaleLanguage'] = $locale[0];
                $_POST['siteLocaleCountry'] = $locale[1];
            } else {
                $_POST['siteLocaleLanguage'] = 'en';
                $_POST['siteLocaleCountry'] = 'US';
            }

            Database::extend('install', function () use ($options) {
                return Database::getFactory()->createConnection(array(
                    'host' => $options['db-server'],
                    'user' => $options['db-username'],
                    'password' => $options['db-password'],
                    'database' => $options['db-database'],
                ));
            });
            Database::setDefaultConnection('install');
            Config::set('database.connections.install', array());

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
                $e->add('concrete5 requires PHP '.$cnt->getMinimumPhpVersion().' or greater.');
            }
            if (is_object($fileWriteErrors)) {
                $e->add($fileWriteErrors);
            }
            $spl = StartingPointPackage::getClass($options['starting-point']);
            if ($spl === null) {
                $e->add('Invalid starting-point: '.$options['starting-point']);
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
                if ($options['canonical-ssl-url']) {
                    $_POST['canonicalSSLUrlChecked'] = '1';
                    $_POST['canonicalSSLUrl'] = $options['canonical-ssl-url'];
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
                    call_user_func(array($spl, $r->getMethod()));
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
                \UserInfo::add(array(
                    'uName' => $options['demo-username'],
                    'uEmail' => $options['demo-email'],
                    'uPassword' => $options['demo-password'],
                ))->getUserObject()->enterGroup(
                    \Group::getByID(ADMIN_GROUP_ID)
                );
                $output->writeln('done.');
            }
            $output->writeln('<info>Installation Complete!</info>');
        } catch (Exception $x) {
            $output->writeln('<error>'.$x->getMessage().'</error>');
            $rc = 1;
        }

        return $rc;
    }
}
