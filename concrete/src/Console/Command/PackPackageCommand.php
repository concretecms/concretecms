<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Package\Packer\Filter\FileExcluder;
use Concrete\Core\Package\Packer\PackagePacker;
use Concrete\Core\Package\Packer\PackagePackerOptions;
use Concrete\Core\Utility\Service\Validation\Strings as StringsValidator;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class PackPackageCommand extends Command
{
    const PACKAGEFORMAT_LEGACY = 'legacy';
    const PACKAGEFORMAT_CURRENT = 'current';

    /**
     * @deprecated use the PackagePackerOptions::CONVERTSHORTTAGS_... constants
     */
    const SHORTTAGS_ALL = PackagePackerOptions::CONVERTSHORTTAGS_ALL;
    /**
     * @deprecated use the PackagePackerOptions::CONVERTSHORTTAGS_... constants
     */
    const SHORTTAGS_KEEPECHO = PackagePackerOptions::CONVERTSHORTTAGS_KEEPECHO;
    /**
     * @deprecated use the PackagePackerOptions::CONVERTSHORTTAGS_... constants
     */
    const SHORTTAGS_NO = PackagePackerOptions::CONVERTSHORTTAGS_NO;

    const KEEP_DOT = 'dot';
    const KEEP_SOURCES = 'sources';
    const KEEP_COMPOSER_JSON = 'composer-json';
    const KEEP_COMPOSER_LOCK = 'composer-lock';

    const YNA_YES = 'yes';
    const YNA_AUTO = 'auto';
    const YNA_NO = 'no';

    const ZIPOUT_AUTO = '-';

    public function handle(PackagePacker $packer, StringsValidator $stringsValidator, PackageService $packageService)
    {
        $options = $this->parseInputOptions();
        $packageDirectory = $this->parseInputArgument($stringsValidator, $packageService);
        $packer->process($packageDirectory, $options);
    }

    protected function configure()
    {
        $zipAuto = static::ZIPOUT_AUTO;
        $keepDot = static::KEEP_DOT;
        $keepSources = static::KEEP_SOURCES;
        $keepComposerJson = static::KEEP_COMPOSER_JSON;
        $keepComposerLock = static::KEEP_COMPOSER_LOCK;
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:package:pack')
            ->setAliases([
                'c5:package-pack',
                'c5:pack-package',
            ])
            ->setDescription('Process a package (expand PHP short tags, compile icons and translations, create zip archive)')
            ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to work on (or the path to a directory containing a concrete5 package)')
            ->addEnvOption()
            ->addOption('short-tags', 's', InputOption::VALUE_REQUIRED, 'Expand PHP short tags [' . PackagePackerOptions::CONVERTSHORTTAGS_ALL . '|' . PackagePackerOptions::CONVERTSHORTTAGS_KEEPECHO . '|' . PackagePackerOptions::CONVERTSHORTTAGS_NO . '|' . PackagePackerOptions::CONVERTSHORTTAGS_AUTO . ']', PackagePackerOptions::CONVERTSHORTTAGS_AUTO)
            ->addOption('compile-icons', 'i', InputOption::VALUE_REQUIRED, 'Compile SVG icons to PNG icons [' . static::YNA_YES . '|' . static::YNA_NO . '|' . static::YNA_AUTO . ']?', static::YNA_AUTO)
            ->addOption('compile-translations', 't', InputOption::VALUE_REQUIRED, 'Compile source .po translation files to the .mo binary format [' . static::YNA_YES . '|' . static::YNA_NO . '|' . static::YNA_AUTO . ']?', static::YNA_AUTO)
            ->addOption('keep', 'k', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which files should be stored in the zip archive [' . static::KEEP_DOT . '|' . static::KEEP_SOURCES . '|' . static::KEEP_COMPOSER_JSON . '|' . static::KEEP_COMPOSER_LOCK . ']')
            ->addOption('update-source-directory', 'u', InputOption::VALUE_NONE, 'Update the files of the source directory (otherwise it remains untouched)')
            ->addOption('zip', 'z', InputOption::VALUE_OPTIONAL, 'Create a zip archive of the package (and optionally set its path)', static::ZIPOUT_AUTO)
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
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-package-pack
EOT
            )
        ;
    }

    /**
     * Convert the command-line options to a PackagePackerOptions instance.
     *
     * @throws \Exception in case of invalid command line options
     *
     * @return \Concrete\Core\Package\Packer\PackagePackerOptions
     */
    protected function parseInputOptions()
    {
        $keepMap = [
            static::KEEP_DOT => FileExcluder::KEEPFILES_DOT,
            static::KEEP_SOURCES => FileExcluder::KEEPFILES_POT | FileExcluder::KEEPFILES_PO | FileExcluder::KEEPFILES_SVGICON,
            static::KEEP_COMPOSER_JSON => FileExcluder::KEEPFILES_COMPOSER_JSON,
            static::KEEP_COMPOSER_LOCK => FileExcluder::KEEPFILES_COMPOSER_LOCK,
        ];
        $ynaMap = [
            static::YNA_YES => true,
            static::YNA_NO => false,
            static::YNA_AUTO => null,
        ];
        if (!array_key_exists($this->input->getOption('compile-icons'), $ynaMap)) {
            throw new Exception('Invalid value of the --compile-icons option');
        }
        if (!array_key_exists($this->input->getOption('compile-translations'), $ynaMap)) {
            throw new Exception('Invalid value of the --compile-translations option');
        }

        $result = PackagePackerOptions::create()
            ->setOutput($this->output)
            ->setShortTagsConversion($this->input->getOption('short-tags'))
            ->setCompileIcons($ynaMap[$this->input->getOption('compile-icons')])
            ->setCompileTranslations($ynaMap[$this->input->getOption('compile-translations')])
            ->setKeepFiles(FileExcluder::KEEPFILES_NONE)
            ->setUpdateSourceFiles($this->input->getOption('update-source-directory'))
        ;
        foreach ($this->input->getOption('keep') as $keep) {
            if (!isset($keepMap[$keep])) {
                throw new Exception('Invalid value of the --keep option: ' . $keep);
            }
            $result->setKeepFiles($result->getKeepFiles() | $keepMap[$keep]);
        }
        $zipOption = $this->input->getOption('zip');
        if ($zipOption === static::ZIPOUT_AUTO) {
            if ($this->input->getParameterOption(['--zip', '-z']) === false) {
                $result->setDestinationArchivePath('');
            } else {
                $result->setDestinationArchivePathAutomatic();
            }
        } elseif ($zipOption === null) {
            $result->setDestinationArchivePathAutomatic();
        } else {
            $result->setDestinationArchivePath($zipOption);
        }
        if ($result->isUpdateSourceFiles() === false && $result->isDestinationArchivePathAutomatic() === false && $result->getDestinationArchivePath() === '') {
            throw new Exception('No operation will be performed: neither a zip archive will be created nor the source directory will be updated');
        }

        return $result;
    }

    /**
     * Convert the command-line argument to the path of the package.
     *
     * @param \Concrete\Core\Utility\Service\Validation\Strings $stringsValidator
     * @param \Concrete\Core\Package\PackageService $packageService
     *
     * @throws \Exception in case of invalid command line arguments
     *
     * @return string
     */
    protected function parseInputArgument(StringsValidator $stringsValidator, PackageService $packageService)
    {
        $packageArgument = $this->input->getArgument('package');
        if (is_dir($packageArgument) && !$stringsValidator->handle($packageArgument)) {
            return $packageArgument;
        }
        $package = null;
        foreach ($packageService->getAvailablePackages(false) as $p) {
            if (strcasecmp($packageArgument, $p->getPackageHandle()) === 0) {
                $package = $p;
                break;
            }
        }
        if ($package === null) {
            throw new Exception("Unable to find a package with handle '{$packageArgument}'");
        }

        return $package->getPackagePath();
    }
}
