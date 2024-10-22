<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Console\ConsoleAwareInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Localization\Service\TranslationsInstaller;
use Concrete\Core\Marketplace\PackageRepositoryInterface;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Exception;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallPackageCommand extends Command
{
    protected function configure()
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;
        $this
            ->setName('c5:package:install')
            ->setAliases([
                'c5:package-install',
                'c5:install-package',
            ])
            ->addOption('full-content-swap', null, InputOption::VALUE_NONE, 'If this option is specified a full content swap will be performed (if the package supports it)')
            ->addOption('languages', 'l', InputOption::VALUE_REQUIRED, 'Force to install ("yes") or to not install ("no") language files. If "auto", language files will be installed if the package is connected to the project ("auto" requires that the canonical URL is set)', 'auto')
            ->setDescription('Install a package')
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to be installed')
            ->addArgument('package-options', InputArgument::IS_ARRAY, 'List of key-value pairs to pass to the package install routine (example: foo=bar baz=foo)')
            ->setHelp(<<<EOT
Returns codes:
  $okExitCode operation completed successfully
  $errExitCode errors occurred

More info at https://documentation.concretecms.org/9-x/developers/security/cli-jobs#c5-package-install
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $packageService = $app->make(PackageService::class);
        $packageRepository = $app->make(PackageRepositoryInterface::class);
        $connection = $packageRepository->getConnection();

        $pkgHandle = $input->getArgument('package');
        switch (strtolower($input->getOption('languages'))) {
            case 'yes':
            case 'y':
                $getLanguages = true;
                break;
            case 'no':
            case 'n':
                $getLanguages = false;
                break;
            case 'auto':
                $associatedPackages = $connection ? $packageRepository->getPackages($connection) : [];
                $getLanguages = (bool) array_first($associatedPackages, function ($pkg) use ($pkgHandle) {
                    return $pkg->handle = $pkgHandle;
                });
                break;
            default:
                throw new InvalidOptionException('Invalid value for the --languages option. Valid values are "yes", "no", "auto"');
        }
        $packageOptions = [];
        foreach ($input->getArgument('package-options') as $keyValuePair) {
            list($key, $value) = explode('=', $keyValuePair, 2);
            $key = trim($key);
            if (substr($key, -2) === '[]') {
                $isArray = true;
                $key = rtrim(substr($key, 0, -2));
            } else {
                $isArray = false;
            }
            if ($key === '' || !isset($value)) {
                throw new Exception(sprintf("Unable to parse the package option '%s': it must be in the form of key=value", $keyValuePair));
            }
            if (isset($packageOptions[$key])) {
                if (!($isArray && is_array($packageOptions[$key]))) {
                    throw new Exception(sprintf("Duplicated package option '%s'", $key));
                }
                $packageOptions[$key][] = $value;
            } else {
                $packageOptions[$key] = $isArray ? ((array) $value) : $value;
            }
        }

        $output->write("Looking for package '$pkgHandle'... ");
        foreach ($packageService->getInstalledList() as $installed) {
            if ($installed->getPackageHandle() === $pkgHandle) {
                throw new Exception(sprintf("The package '%s' (%s) is already installed", $pkgHandle, $installed->getPackageName()));
            }
        }
        $pkg = null;
        foreach ($packageService->getAvailablePackages() as $available) {
            if ($available->getPackageHandle() === $pkgHandle) {
                $pkg = $available;
                break;
            }
        }
        if ($pkg === null) {
            throw new Exception(sprintf("No package with handle '%s' was found", $pkgHandle));
        }

        // Provide the console objects to objects that are aware of the console
        if ($pkg instanceof ConsoleAwareInterface) {
            $pkg->setConsole($this->getApplication(), $output, $input);
        }

        $output->writeln(sprintf('<info>found (%s).</info>', $pkg->getPackageName()));

        $output->write('Checking preconditions... ');
        $test = $pkg->testForInstall();
        if (is_object($test)) {
            throw new Exception(implode("\n", $test->getList()));
        }
        $output->writeln('<info>passed.</info>');

        $output->write('Installing... ');
        $r = $packageService->install($pkg, $packageOptions);
        if ($r instanceof ErrorList) {
            throw new Exception(implode("\n", $r->getList()));
        }
        $output->writeln('<info>installed.</info>');

        if ($getLanguages) {
            $output->write('Fetching language files... ');
            $languageResult = $app->make(TranslationsInstaller::class)->installMissingPackageTranslations($pkg);
            if (count($languageResult) === 0) {
                $output->writeln('<info>no languages downloaded.</info>');
            } else {
                $output->writeln('done. Results:');
                foreach ($languageResult as $localeID => $result) {
                    if ($result === true) {
                        $output->writeln(" - {$localeID}: <info>downloaded</info>");
                    } elseif ($result === false) {
                        $output->writeln(" - {$localeID}: <error>non available</error>");
                    } else {
                        $output->writeln(" - $localeID: <error>" . ((string) $result) . '</error>');
                    }
                }
            }
        }

        $swapper = $pkg->getContentSwapper();
        if ($swapper->allowsFullContentSwap($pkg) && $input->getOption('full-content-swap')) {
            $options = [];

            if (count($pkg->getContentSwapFiles()) > 1) {
                $contentSwapFiles = [];
                $contentSwapTemplateNames = [];
                $index = 1;

                foreach($pkg->getContentSwapFiles() as $contentSwapFile => $contentSwapTemplateName) {
                    $contentSwapFiles[$index] = $contentSwapFile;
                    $contentSwapTemplateNames[] = sprintf("%s: %s", $index, $contentSwapTemplateName);
                    $index++;
                }
                $io = new SymfonyStyle($input, $output);
                $io->listing($contentSwapTemplateNames);

                $helper = $this->getHelper('question');
                $selectedContentSwapFile = $helper->ask($input, $output, new Question('Select the number of the content swap file you want to install: ', 1));

                if (isset($contentSwapFiles[$selectedContentSwapFile])) {
                    $options["contentSwapFile"] = $contentSwapFiles[$selectedContentSwapFile];
                }
            }

            $output->write('Performing full content swap... ');
            $swapper->swapContent($pkg, $options);
            if (method_exists($pkg, 'on_after_swap_content')) {
                $pkg->on_after_swap_content([]);
            }
            $output->writeln('<info>done.</info>');
        }

        return static::SUCCESS;
    }
}
