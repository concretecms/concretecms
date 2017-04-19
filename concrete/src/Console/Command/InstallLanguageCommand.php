<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Service\TranslationsChecker;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Localization\Translation\PackageLocaleStatus;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallLanguageCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
        ->setName('c5:language-install')
        ->setDescription('Install or update concrete5 languages')
        ->addEnvOption()
        ->addOption('--update', 'u', InputOption::VALUE_NONE, 'Update any outdated language files')
        ->addOption('--add', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Add new language files')
        ->addOption('--core', 'c', InputOption::VALUE_NONE, 'Process only a the concrete5 core')
        ->addOption('--packages', 'p', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Process only packages (you can specify one or more package handle too)')
        ->setHelp(<<<EOT
Examples:
            
# to update all the outdated language files (for the concrete5 core and for all the packages)
$ concrete/bin/concrete5 c5:language-install --update
            
# to update all the outdated language files (for the concrete5 core only)
$ concrete/bin/concrete5 c5:language-install --update --core
            
# to update all the outdated language files (for any package)
$ concrete/bin/concrete5 c5:language-install --update --packages
            
# to update all the outdated language files (for specific packages only)
$ concrete/bin/concrete5 c5:language-install --update --packages=handyman --packages=lets_encrypt
            
# to add new languages (for the concrete5 core and for all the packages)
$ concrete/bin/concrete5 c5:language-install --add it_IT --add de_DE
            
# to add new languages (for the concrete5 core only)
$ concrete/bin/concrete5 c5:language-install --add it_IT --add de_DE --core
            
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred
            
More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-language-install
EOT
            );
    }

    /**
     * @var \Concrete\Core\Application\Application|null
     */
    protected $app;

    /**
     * @var TranslationsChecker|null
     */
    protected $translationsChecker;

    /**
     * @var TranslationsInstaller|null
     */
    protected $translationsInstaller;

    /**
     * @var OutputInterface|null
     */
    protected $output;

    /**
     * @var bool|null
     */
    protected $shouldClearLocalizationCache;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('update') && count($input->getOption('add')) === 0) {
            throw new Exception('Please specify at least the --update or the --add option');
        }

        $this->app = Application::getFacadeApplication();
        $this->translationsChecker = $this->app->make(TranslationsChecker::class);
        $this->translationsInstaller = $this->app->make(TranslationsInstaller::class);
        $this->output = $output;
        $this->shouldClearLocalizationCache = false;

        $processCore = $this->checkCoreFlag($input);
        $processPackages = $this->checkPackagesFlag($input);

        $data = $this->getTranslationsData($processCore, $processPackages);

        if ($input->getOption('update')) {
            $this->updateLanguages($data);
        }
        if (count($input->getOption('add')) > 0) {
            $this->addLanguages($data, $input->getOption('add'));
        }
        if ($this->shouldClearLocalizationCache) {
            Localization::clearCache();
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return bool
     */
    private function checkCoreFlag(InputInterface $input)
    {
        return $input->getOption('core') || count($input->getOption('packages')) === 0;
    }

    /**
     * @param InputInterface $input
     *
     * @return Package
     */
    private function checkPackagesFlag(InputInterface $input)
    {
        $result = [];
        if (!$input->getOption('core') || count($input->getOption('packages')) > 0) {
            $pkgList = $input->getOption('packages');
            if ($pkgList === [null]) {
                $pkgList = [];
            }
            $ps = $this->app->make(PackageService::class);
            /* @var PackageService $ps */
            $allPackages = $ps->getAvailablePackages(false);
            if (count($pkgList) === 0) {
                $result = $allPackages;
            } else {
                foreach ($pkgList as $pkg) {
                    $package = null;
                    foreach ($allPackages as $p) {
                        if (strcasecmp($p->getPackageHandle(), $pkg) === 0) {
                            $package = $p;
                            break;
                        }
                    }
                    if ($package === null) {
                        throw new Exception(sprintf('Unable to find a package with handle %s', $pkg));
                    }
                    if (!in_array($package, $result, true)) {
                        $result[] = $package;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param bool $processCore
     * @param Package[] $processPackages
     *
     * @return \Concrete\Core\Localization\Translation\LocaleStatus[]
     */
    private function getTranslationsData($processCore, array $processPackages)
    {
        if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $this->output->write('# Fetching list of translations... ');
        }
        $result = [];
        if ($processCore) {
            $result[] = $this->translationsChecker->getCoreTranslations();
        }
        foreach ($processPackages as $package) {
            $result[] = $this->translationsChecker->getPackageTranslations($package);
        }
        if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('done.');
        }

        return $result;
    }

    /**
     * @param \Concrete\Core\Localization\Translation\LocaleStatus[] $data
     */
    private function updateLanguages(array $data)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('# Updating languages');
        }
        $numUpdated = 0;
        foreach ($data as $details) {
            $numUpdated += $this->updateLanguagesFor($details->getInstalledOutdated(), ($details instanceof PackageLocaleStatus) ? $details->getPackage() : null);
        }
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('  Number of languages updated: ' . $numUpdated);
        }
    }

    /**
     * @param \Concrete\Core\Localization\Translation\LocalRemoteCouple[] $installedOutdated
     * @param Package $package
     *
     * @return int
     */
    private function updateLanguagesFor(array $installedOutdated, Package $package = null)
    {
        $result = 0;
        if (empty($installedOutdated)) {
            if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                if ($package === null) {
                    $this->output->writeln('  > no updates for concrete5 core');
                } else {
                    $this->output->writeln(sprintf('  > no updates for package %s', $package->getPackageHandle()));
                }
            }
        } else {
            if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                if ($package === null) {
                    $this->output->writeln('  > updating concrete5 core');
                } else {
                    $this->output->writeln(sprintf('> updating package %s', $package->getPackageHandle()));
                }
            }
            foreach ($installedOutdated as $localeID => $rl) {
                if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                    $this->output->write('    - ' . $localeID . '... ');
                }
                if ($package === null) {
                    $this->translationsInstaller->installCoreTranslations($localeID);
                } else {
                    $this->translationsInstaller->installPackageTranslations($package, $localeID);
                }
                $this->shouldClearLocalizationCache = true;
                ++$result;
                if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                    $this->output->writeln('done.');
                }
            }
        }

        return $result;
    }

    /**
     * @param \Concrete\Core\Localization\Translation\LocaleStatus[] $data
     * @param string[] $localeIDs
     */
    private function addLanguages(array $data, array $localeIDs)
    {
        foreach ($localeIDs as $localeID) {
            $this->addLanguage($data, $localeID);
        }
    }

    /**
     * @param \Concrete\Core\Localization\Translation\LocaleStatus[] $data
     * @param string $localeIDs
     */
    private function addLanguage(array $data, $localeID)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('# Adding language ' . $localeID);
        }
        $numAdded = 0;
        foreach ($data as $details) {
            if ($this->addLanguageFor($details->getOnlyRemote(), $localeID, ($details instanceof PackageLocaleStatus) ? $details->getPackage() : null)) {
                ++$numAdded;
            }
        }
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $this->output->writeln('  Number of language files added: ' . $numAdded);
        }
    }

    /**
     * @param \Concrete\Core\Localization\Translation\Remote\Stats[] $availableRemoteStats
     * @param Package $package
     *
     * @return bool
     */
    private function addLanguageFor(array $availableRemoteStats, $localeID, Package $package = null)
    {
        if (isset($availableRemoteStats[$localeID])) {
            if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                if ($package === null) {
                    $this->output->write(sprintf('  > installing language %s for concrete5 core... ', $localeID));
                } else {
                    $this->output->write(sprintf('  > installing %s for package %s... ', $localeID, $package->getPackageHandle()));
                }
            }
            if ($package === null) {
                $this->translationsInstaller->installCoreTranslations($localeID);
            } else {
                $this->translationsInstaller->installPackageTranslations($package, $localeID);
            }
            $this->shouldClearLocalizationCache = true;
            $result = true;
            if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                $this->output->writeln('done.');
            }
        } else {
            if ($this->output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                if ($package === null) {
                    $this->output->writeln(sprintf('  > language %s is not available (or it\'s already installed) for concrete5 core', $localeID));
                } else {
                    $this->output->writeln(sprintf('  > language %s is not available (or it\'s already installed) for package %s', $localeID, $package->getPackageHandle()));
                }
            }
            $result = false;
        }

        return $result;
    }
}
