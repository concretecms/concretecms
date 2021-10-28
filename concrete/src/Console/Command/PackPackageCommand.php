<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Console\Command;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Package\Offline\Inspector;
use Concrete\Core\Package\Offline\PackageInfo;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Package\Packer\Filter\FileExcluder;
use Concrete\Core\Package\Packer\Filter\ShortTagExpander;
use Concrete\Core\Package\Packer\Filter\SvgIconRasterizer;
use Concrete\Core\Package\Packer\Filter\TranslationCompiler;
use Concrete\Core\Package\Packer\PackagePacker;
use Concrete\Core\Package\Packer\Writer\Cloner;
use Concrete\Core\Package\Packer\Writer\SourceUpdater;
use Concrete\Core\Package\Packer\Writer\Zipper;
use Concrete\Core\Utility\Service\Validation\Strings as StringsValidator;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class PackPackageCommand extends Command
{
    /**
     * Convert all short PHP tags to long PHP tags (including short echo tags).
     *
     * @var string
     */
    const SHORTTAGS_ALL = 'all';

    /**
     * Convert short PHP tags to long PHP tags (excluding short echo tags).
     *
     * @var string
     */
    const SHORTTAGS_KEEPECHO = 'keep-echo';

    /**
     * Use SHORTTAGS_ALL for Concrete 5.x packages, SHORTTAGS_KEEPECHO for Concrete 8+ packages.
     *
     * @var string
     */
    const SHORTTAGS_AUTO = 'auto';

    /**
     * Do not convert any short PHP tags.
     *
     * @var string
     */
    const SHORTTAGS_NO = 'no';

    /**
     * Keep files starting with a dot.
     *
     * @var string
     */
    const KEEP_DOT = 'dot';

    /**
     * Keep translation .po files and icon source .svg files.
     *
     * @var string
     */
    const KEEP_SOURCES = 'sources';

    /**
     * Keep composer.json files.
     *
     * @var string
     */
    const KEEP_COMPOSER_JSON = 'composer-json';

    /**
     * Keep composer.json and composer.lock files.
     *
     * @var string
     */
    const KEEP_COMPOSER_LOCK = 'composer-lock';

    /**
     * Option for boolean/automatic flags: yes.
     *
     * @var string
     */
    const YNA_YES = 'yes';

    /**
     * Option for boolean/automatic flags: automatic.
     *
     * @var string
     */
    const YNA_AUTO = 'auto';

    /**
     * Option for boolean/automatic flags: no.
     *
     * @var string
     */
    const YNA_NO = 'no';

    /**
     * Value of the zip option to be used to automatically determine the .zip file name.
     *
     * @var string
     */
    const ZIPOUT_AUTO = '-';

    /**
     * Execute the command.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Package\Offline\Inspector $packageInspector
     * @param \Concrete\Core\Package\Packer\PackagePacker $packer
     * @param \Concrete\Core\Utility\Service\Validation\Strings $stringsValidator
     * @param \Concrete\Core\Package\PackageService $packageService
     */
    public function handle(Application $app, Inspector $packageInspector, PackagePacker $packer, StringsValidator $stringsValidator, PackageService $packageService)
    {
        $packageInfo = $this->parseInputArgument($packageInspector, $stringsValidator, $packageService);
        $filters = $this->createFilters($app, $packageInfo);
        $writers = $this->createWriters($app, $packageInfo);
        $packer->process($packageInfo, $filters, $writers);

        return static::SUCCESS;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $zipAuto = self::ZIPOUT_AUTO;
        $keepDot = self::KEEP_DOT;
        $keepSources = self::KEEP_SOURCES;
        $keepComposerJson = self::KEEP_COMPOSER_JSON;
        $keepComposerLock = self::KEEP_COMPOSER_LOCK;
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;
        $this
            ->setName('c5:package:pack')
            ->setAliases([
                'c5:package-pack',
                'c5:pack-package',
            ])
            ->setDescription('Process a package (expand PHP short tags, compile icons and translations, create zip archive)')
            ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to work on (or the path to a directory containing a Concrete package)')
            ->addEnvOption()
            ->addOption('short-tags', 's', InputOption::VALUE_REQUIRED, 'Expand PHP short tags [' . self::SHORTTAGS_ALL . '|' . self::SHORTTAGS_KEEPECHO . '|' . self::SHORTTAGS_NO . '|' . self::SHORTTAGS_AUTO . ']', self::SHORTTAGS_AUTO)
            ->addOption('compile-icons', 'i', InputOption::VALUE_REQUIRED, 'Compile SVG icons to PNG icons [' . self::YNA_YES . '|' . self::YNA_NO . '|' . self::YNA_AUTO . ']?', self::YNA_AUTO)
            ->addOption('compile-translations', 't', InputOption::VALUE_REQUIRED, 'Compile source .po translation files to the .mo binary format [' . self::YNA_YES . '|' . self::YNA_NO . '|' . self::YNA_AUTO . ']?', self::YNA_AUTO)
            ->addOption('keep', 'k', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which files should be stored in the zip archive [' . self::KEEP_DOT . '|' . self::KEEP_SOURCES . '|' . self::KEEP_COMPOSER_JSON . '|' . self::KEEP_COMPOSER_LOCK . ']')
            ->addOption('update-source-directory', 'u', InputOption::VALUE_NONE, 'Update the files of the source directory (otherwise it remains untouched)')
            ->addOption('zip', 'z', InputOption::VALUE_OPTIONAL, 'Create a zip archive of the package (and optionally set its path)', self::ZIPOUT_AUTO)
            ->addOption('copy', 'c', InputOption::VALUE_REQUIRED, 'Copy the package files to another directory')
            ->addOption('overwrite', 'o', InputOption::VALUE_NONE, 'Copy the package files to another directory even if it already exists')
            ->setHelp(<<<EOT
You have to specify at least the -z option (to create a zip file) and/or the -u option (to update the package directory).

If the -u option is not specified, the package directory is not touched.

If the -z option is specified without a value (or with a value of "$zipAuto"), the zip file will be created in the directory containing the package folder.
If the -z option is set and the zip file already exists, it will be overwritten.

To include in the zip archive the files and directories starting with a dot, use the "-k $keepDot" option.
To include in the zip archive the source files for translations (.po files) and icons (.svg files) use the "-k $keepSources" option.
To include in the zip archive the composer.json and composer.lock files, use the "-k $keepComposerLock" option.
To include in the zip archive the composer.json file but not the composer.lock files, use the "-k $keepComposerJson" option.

Please remark that this command can also parse legacy (pre-5.7) packages.

Returns codes:
  $okExitCode operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-package-pack
EOT
            )
        ;
    }

    /**
     * Get the package details analyzing the command-line arguments.
     *
     * @param \Concrete\Core\Package\Offline\Inspector $packageInspector
     * @param \Concrete\Core\Utility\Service\Validation\Strings $stringsValidator
     * @param \Concrete\Core\Package\PackageService $packageService
     *
     * @throws \Exception in case of invalid command line arguments
     *
     * @return \Concrete\Core\Package\Offline\PackageInfo
     */
    protected function parseInputArgument(Inspector $packageInspector, StringsValidator $stringsValidator, PackageService $packageService)
    {
        $packageArgument = $this->input->getArgument('package');
        if (is_dir($packageArgument) && !$stringsValidator->handle($packageArgument)) {
            return $packageInspector->inspectPackageDirectory($packageArgument);
        }
        foreach ($packageService->getAvailablePackages(false) as $package) {
            if (strcasecmp($packageArgument, $package->getPackageHandle()) === 0) {
                return $packageInspector->inspectPackageDirectory($package->getPackagePath());
            }
        }
        throw new Exception("Unable to find a package with handle '{$packageArgument}'");
    }

    /**
     * Generate the filters analyzing the command-line options.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Package\Offline\PackageInfo the package info for which the options should be related to
     * @param PackageInfo $packageInfo
     *
     * @throws \Exception in case of invalid command line options
     *
     * @return \Concrete\Core\Package\Packer\Filter\FilterInterface[]
     */
    protected function createFilters(Application $app, PackageInfo $packageInfo)
    {
        $volatileDirectory = $app->make(VolatileDirectory::class);
        $filters = [];
        switch ($this->input->getOption('compile-icons')) {
            case self::YNA_YES:
                $filters[] = $app->make(SvgIconRasterizer::class, ['coreVersion' => $packageInfo->getMayorMinimumCoreVersion(), 'checkEditDate' => false, 'output' => $this->output, 'volatileDirectory' => $volatileDirectory]);
                break;
            case self::YNA_AUTO:
                $filters[] = $app->make(SvgIconRasterizer::class, ['coreVersion' => $packageInfo->getMayorMinimumCoreVersion(), 'checkEditDate' => true, 'output' => $this->output, 'volatileDirectory' => $volatileDirectory]);
                break;
            case self::YNA_NO:
                break;
            default:
                throw new Exception('Invalid value of the --compile-icons option');
        }
        switch ($this->input->getOption('compile-translations')) {
            case self::YNA_YES:
                $filters[] = $app->make(TranslationCompiler::class, ['checkEditDate' => false, 'output' => $this->output, 'volatileDirectory' => $volatileDirectory]);
                break;
            case self::YNA_AUTO:
                $filters[] = $app->make(TranslationCompiler::class, ['checkEditDate' => true, 'output' => $this->output, 'volatileDirectory' => $volatileDirectory]);
                break;
            case self::YNA_NO:
                break;
            default:
                throw new Exception('Invalid value of the --compile-icons option');
        }
        $shortTags = $this->input->getOption('short-tags');
        if ($shortTags === self::SHORTTAGS_AUTO) {
            $shortTags = version_compare($packageInfo->getMayorMinimumCoreVersion(), '8') < 0 ? self::SHORTTAGS_ALL : self::SHORTTAGS_KEEPECHO;
        }
        switch ($shortTags) {
            case self::SHORTTAGS_ALL:
                $filters[] = $app->make(ShortTagExpander::class, ['expandEcho' => true, 'output' => $this->output, 'volatileDirectory' => $volatileDirectory]);
                break;
            case self::SHORTTAGS_KEEPECHO:
                $filters[] = $app->make(ShortTagExpander::class, ['expandEcho' => false, 'output' => $this->output, 'volatileDirectory' => $volatileDirectory]);
                break;
            case self::SHORTTAGS_NO:
                break;
            default:
                throw new Exception('Invalid value of the --short-tags option');
        }
        $keepMap = [
            self::KEEP_DOT => FileExcluder::EXCLUDE_DOT,
            self::KEEP_SOURCES => FileExcluder::EXCLUDE_POT | FileExcluder::EXCLUDE_PO | FileExcluder::EXCLUDE_SVGICON,
            self::KEEP_COMPOSER_JSON => FileExcluder::EXCLUDE_COMPOSER_JSON,
            self::KEEP_COMPOSER_LOCK => FileExcluder::EXCLUDE_COMPOSER_LOCK,
        ];
        $keepOptions = $this->input->getOption('keep');
        $invalidOptions = array_diff($keepOptions, array_keys($keepMap));
        if (count($invalidOptions) !== 0) {
            throw new Exception('Invalid values of the --keep option: ' . implode(', ', $invalidOptions));
        }
        $excludeFlags = FileExcluder::EXCLUDE_NONE;
        foreach ($keepMap as $keepOption => $keepOptionFlags) {
            if (!in_array($keepOption, $keepOptions, true)) {
                $excludeFlags |= $keepOptionFlags;
            }
        }
        $filters[] = $app->make(FileExcluder::class, ['excludeFiles' => $excludeFlags, 'output' => $this->output]);

        return $filters;
    }

    /**
     * Generate the writers analyzing the command-line options.
     *
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Package\Offline\PackageInfo the package info for which the options should be related to
     * @param PackageInfo $packageInfo
     *
     * @throws \Exception in case of invalid command line options
     *
     * @return \Concrete\Core\Package\Packer\Writer\WriterInterface[]
     */
    protected function createWriters(Application $app, PackageInfo $packageInfo)
    {
        $writers = [];
        if ($this->input->getOption('update-source-directory')) {
            $writers[] = $app->make(SourceUpdater::class, ['basePath' => $packageInfo->getPackageDirectory(), 'output' => $this->output]);
        }
        if ($this->input->getParameterOption(['--zip', '-z']) !== false) {
            $zipOption = (string) $this->input->getOption('zip');
            if ($zipOption === '' || $zipOption === self::ZIPOUT_AUTO) {
                $zipFilename = dirname($packageInfo->getPackageDirectory()) . '/' . $packageInfo->getHandle() . '-' . $packageInfo->getVersion() . '.zip';
            } else {
                $zipFilename = $zipOption;
            }
            $writers[] = $app->make(Zipper::class, ['zipFilename' => $zipFilename, 'rootDirectory' => $packageInfo->getHandle(), 'output' => $this->output]);
        }
        if ($this->input->getOption('copy') !== null) {
            $writers[] = $app->make(Cloner::class, ['destinationDirectory' => $this->input->getOption('copy'), 'overwrite' => $this->input->getOption('overwrite'), 'output' => $this->output]);
        }
        if (empty($writers)) {
            throw new Exception('No operation will be performed');
        }

        return $writers;
    }
}
