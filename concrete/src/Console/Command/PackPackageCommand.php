<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Core;
use Exception;
use Package;
use stdClass;
use ZipArchive;
use Gettext\Translations;

final class PackPackageCommand extends Command
{
    const PACKAGEFORMAT_LEGACY = 'legacy';
    const PACKAGEFORMAT_CURRENT = 'current';

    const SHORTTAGS_ALL = 'all';
    const SHORTTAGS_KEEPECHO = 'keep-echo';
    const SHORTTAGS_NO = 'no';

    const KEEP_DOT = 'dot';
    const KEEP_SOURCES = 'sources';

    const YNA_YES = 'yes';
    const YNA_AUTO = 'auto';
    const YNA_NO = 'no';

    const ZIPOUT_AUTO = '-';

    protected function configure()
    {
        $zipAuto = static::ZIPOUT_AUTO;
        $keepDot = static::KEEP_DOT;
        $keepSources = static::KEEP_SOURCES;
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:package-pack')
            ->setDescription('Process a package (expand PHP short tags, compile icons and translations, create zip archive)')
            ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to work on (or the path to a directory containing a concrete5 package)')
            ->addEnvOption()
            ->addOption('short-tags', 's', InputOption::VALUE_REQUIRED, 'Expand PHP short tags [' . static::SHORTTAGS_ALL . '|' . static::SHORTTAGS_KEEPECHO . '|' . static::SHORTTAGS_NO . ']', static::SHORTTAGS_ALL)
            ->addOption('compile-icons', 'i', InputOption::VALUE_REQUIRED, 'Compile SVG icons to PNG icons [' . static::YNA_YES . '|' . static::YNA_NO . '|' . static::YNA_AUTO . ']?', static::YNA_AUTO)
            ->addOption('compile-translations', 't', InputOption::VALUE_REQUIRED, 'Compile source .po translation files to the .mo binary format [' . static::YNA_YES . '|' . static::YNA_NO . '|' . static::YNA_AUTO . ']?', static::YNA_AUTO)
            ->addOption('keep', 'k', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Which files should be stored in the zip archive [' . static::KEEP_DOT . '|' . static::KEEP_SOURCES . ']')
            ->addOption('update-source-directory', 'u', InputOption::VALUE_NONE, 'Update the files of the source directory (otherwise it remains untouched)')
            ->addOption('zip', 'z', InputOption::VALUE_OPTIONAL, 'Create a zip archive of the package (and optionally set its path)', static::ZIPOUT_AUTO)
            ->setHelp(<<<EOT
You have to specify at least the -z option (to create a zip file) and/or the -u option (to update the package directory).

If the -u option is not specified, the package directory is not touched.

If the -z option is specified without a value (or with a value of "$zipAuto"), the zip file will be created in the directory containing the package folder.
If the -z option is set and the zip file already exists, it will be overwritten.

To include in the zip archive the files and directories starting with a dot, use the "-k $keepDot" option.
To include in the zip archive the source files for translations (.po files) and icons (.svg files) use the "-k $keepSources" option.

Please remark that this command can also parse legacy (pre-5.7) packages.

Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-package-pack
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $state = static::parseInput($input);
            if ($state->updateSourceDirectory === false && $state->zipFilename === null) {
                throw new Exception('No operation will be performed: neither a zip archive will be created nor the source directory will be updated');
            }
            $state->zip = null;
            if ($state->zipFilename !== null) {
                if (@file_exists($state->zipFilename)) {
                    @unlink($state->zipFilename);
                    if (@file_exists($state->zipFilename)) {
                        throw new Exception("Unable to delete the file {$state->zipFilename}");
                    }
                }
                $state->zip = new ZipArchive();
                if (@$state->zip->open($state->zipFilename, ZipArchive::CREATE | ZipArchive::CHECKCONS) !== true) {
                    $err = @$state->zip->getStatusString();
                    if (!$err) {
                        $err = "Failed to create the file {$state->zipFilename}";
                    }
                    throw new Exception($err);
                }
            }
            static::processDirectory($state, $output, $state->packageDirectory, '');
            if ($state->zip !== null) {
                $state->zip->close();
                $state->zip = null;
            }
            if ($state->updateSourceDirectory) {
                $output->writeln('Package folder has been updated');
            }
            if ($state->zipFilename !== null) {
                $output->writeln('Zip archive created: ' . $state->zipFilename);
            }
        } catch (Exception $x) {
            if ($state->zip !== null) {
                $state->zip->close();
                $state->zip = null;
                @unlink($state->zipFilename);
            }
            throw $x;
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return stdClass {
     *
     *  @var string $packageHandle The package handle
     *  @var string $packageDirectory The package directory
     *  @var string|null $packageVersion The package version (if retrieved)
     *  @var string|null $packageFormat The package format (if retrieved) - One of the PackPackageCommand::PACKAGEFORMAT_ constants
     *  @var string $shortTags One of the PackPackageCommand::SHORTTAGS_ constants
     *  @var string $compileIcons One of the PackPackageCommand::YNA_ constants
     *  @var string $compileTranslations One of the PackPackageCommand::YNA_ constants
     *  @var array $keep List of PackPackageCommand::KEEP_ constants
     *  @var bool $updateSourceDirectory
     *  @var string|null $zipFilename
     * }
     */
    protected static function parseInput(InputInterface $input)
    {
        $result = new stdClass();

        $vsh = Core::make('helper/validation/strings');
        /* @var \Concrete\Core\Utility\Service\Validation\Strings $vsh */
        $fh = Core::make('helper/file');
        /* @var \Concrete\Core\File\Service\File $fh */

        $result->packageHandle = null;
        $result->packageDirectory = null;
        $result->packageVersion = null;
        $result->packageFormat = null;
        $p = $input->getArgument('package');
        if (is_dir($p) || !$vsh->handle($p)) {
            $result->packageDirectory = @realpath($p);
            if ($result->packageDirectory === false) {
                throw new Exception("Unable to find the directory '$p'");
            }
            $result->packageDirectory = str_replace(DIRECTORY_SEPARATOR, '/', $result->packageDirectory);
            $controllerFile = $result->packageDirectory . '/' . FILENAME_CONTROLLER;
            if (!is_file($controllerFile)) {
                throw new Exception("The directory '{$result->packageDirectory}' does not seems to contain a valid concrete5 package");
            }
            $controllerContents = $fh->getContents($controllerFile);
            if ($controllerContents) {
                $allTokens = @token_get_all($controllerContents);
                if ($allTokens) {
                    $tokens = array_values(
                        array_filter(
                            $allTokens,
                            function ($token) {
                                $keep = true;
                                if (is_array($token)) {
                                    switch ($token[0]) {
                                        case T_DOC_COMMENT:
                                        case T_WHITESPACE:
                                        case T_COMMENT:
                                        $keep = false;
                                        break;
                                    }
                                }

                                return $keep;
                            }
                            )
                        );
                    $packageAppVersionRequired = null;
                    // Look for package info
                    for ($i = 0; $i < count($tokens) - 2; ++$i) {
                        if (
                            $result->packageHandle === null
                            && is_array($tokens[$i + 0]) && $tokens[$i + 0][0] === T_VARIABLE && $tokens[$i + 0][1] === '$pkgHandle'
                            && is_string($tokens[$i + 1]) && $tokens[$i + 1] === '='
                            && is_array($tokens[$i + 2]) && $tokens[$i + 2][0] === T_CONSTANT_ENCAPSED_STRING
                        ) {
                            $result->packageHandle = @eval('return ' . $tokens[$i + 2][1] . ';');
                            if (!is_string($result->packageHandle) || $result->packageHandle === '') {
                                $result->packageHandle = null;
                            }
                        }
                        if (
                            $result->packageVersion === null
                            && is_array($tokens[$i + 0]) && $tokens[$i + 0][0] === T_VARIABLE && $tokens[$i + 0][1] === '$pkgVersion'
                            && is_string($tokens[$i + 1]) && $tokens[$i + 1] === '='
                            && is_array($tokens[$i + 2]) && $tokens[$i + 2][0] === T_CONSTANT_ENCAPSED_STRING
                        ) {
                            $result->packageVersion = @eval('return ' . $tokens[$i + 2][1] . ';');
                            if (!is_string($result->packageVersion) || $result->packageVersion === '') {
                                $result->packageVersion = null;
                            }
                        }
                        if (
                            $packageAppVersionRequired === null
                            && is_array($tokens[$i + 0]) && $tokens[$i + 0][0] === T_VARIABLE && $tokens[$i + 0][1] === '$appVersionRequired'
                            && is_string($tokens[$i + 1]) && $tokens[$i + 1] === '='
                            && is_array($tokens[$i + 2]) && $tokens[$i + 2][0] === T_CONSTANT_ENCAPSED_STRING
                        ) {
                            $packageAppVersionRequired = @eval('return ' . $tokens[$i + 2][1] . ';');
                            if (!is_string($packageAppVersionRequired) || $packageAppVersionRequired === '') {
                                $packageAppVersionRequired = null;
                            }
                        }
                    }
                    if ($packageAppVersionRequired !== null) {
                        if (version_compare($packageAppVersionRequired, '5.7') < 0) {
                            $result->packageFormat = self::PACKAGEFORMAT_LEGACY;
                        } else {
                            $result->packageFormat = self::PACKAGEFORMAT_CURRENT;
                        }
                    }
                }
            }
            if ($result->packageHandle === null) {
                $result->packageHandle = basename($result->packageDirectory);
            }
        } else {
            foreach (Package::getAvailablePackages(false) as $pkg) {
                if (strcasecmp($p, $pkg->getPackageHandle()) === 0) {
                    $result->packageHandle = $pkg->getPackageHandle();
                    $result->packageDirectory = $pkg->getPackagePath();
                    $result->packageVersion = $pkg->getPackageVersion();
                    $result->packageFormat = self::PACKAGEFORMAT_CURRENT;
                    break;
                }
            }
            if ($result->packageHandle === null) {
                throw new Exception("Unable to find a package with handle '$p'");
            }
        }

        $v = $input->getOption('short-tags');
        switch ($v) {
            case static::SHORTTAGS_ALL:
            case static::SHORTTAGS_KEEPECHO:
            case static::SHORTTAGS_NO:
                $result->shortTags = $v;
                break;
            default:
                throw new Exception('Invalid value of the --short-tags option: ' . $v);
        }
        $v = $input->getOption('compile-icons');
        switch ($v) {
            case static::YNA_YES:
            case static::YNA_AUTO:
            case static::YNA_NO:
                $result->compileIcons = $v;
                break;
            default:
                throw new Exception('Invalid value of the --compile-icons option: ' . $v);
        }
        $v = $input->getOption('compile-translations');
        switch ($v) {
            case static::YNA_YES:
            case static::YNA_AUTO:
            case static::YNA_NO:
                $result->compileTranslations = $v;
                break;
            default:
                throw new Exception('Invalid value of the --compile-translations option: ' . $v);
        }
        $result->keep = [];
        foreach ($input->getOption('keep') as $keep) {
            if (!in_array($keep, $result->keep)) {
                switch ($keep) {
                    case static::KEEP_DOT:
                    case static::KEEP_SOURCES:
                        $result->keep[] = $keep;
                        break;
                    default:
                        throw new Exception('Invalid value of the --keep option: ' . $keep);
                }
            }
        }
        $result->updateSourceDirectory = (bool) $input->getOption('update-source-directory');

        $result->zipFilename = null;
        $zipOption = $input->getOption('zip');
        if ($zipOption === static::ZIPOUT_AUTO) {
            if ($input->getParameterOption(['--zip', '-z']) === false) {
                $zipOption = null;
            }
        }
        if ($zipOption !== null) {
            if ($zipOption === static::ZIPOUT_AUTO) {
                $zipOption = dirname($result->packageDirectory);
            }
            if (is_dir($zipOption)) {
                $dir = @realpath($zipOption);
                if ($dir === false) {
                    throw new Exception('Unable to normalize the directory ' . $zipOption);
                }
                $dir = str_replace(DIRECTORY_SEPARATOR, '/', $dir);
                $result->zipFilename = $dir . '/' . $result->packageHandle;
                if ($result->packageVersion !== null) {
                    $result->zipFilename .= '-' . $result->packageVersion;
                }
                $result->zipFilename .= '.zip';
            } else {
                $result->zipFilename = $zipOption;
            }
        }

        return $result;
    }

    private static function processDirectory($state, OutputInterface $output, $dirAbs, $dirRel)
    {
        $hDir = @opendir($dirAbs);
        if ($hDir === false) {
            throw new Exception("Failed to open directory $dirAbs");
        }
        try {
            $storePrefix = $state->packageHandle;
            if ($dirRel !== '') {
                $storePrefix .= "/$dirRel";
            }
            if ($state->zip !== null) {
                if (@$state->zip->addEmptyDir($storePrefix) === false) {
                    $err = "Failed to create directory entry $storePrefix in zip file";
                    $reason = $state->zip->getStatusString();
                    if ($reason) {
                        $err .= ": $reason";
                    }
                    throw new Exception($err);
                }
            }
            $store = [];
            $subDirs = [];
            while (($item = readdir($hDir)) !== false) {
                if (($item === '.') || ($item === '..')) {
                    continue;
                }
                if ($item[0] === '.' && !in_array(static::KEEP_DOT, $state->keep)) {
                    continue;
                }
                $itemAbs = $dirAbs . '/' . $item;
                if (is_dir($itemAbs)) {
                    $subDirs[] = $item;
                    continue;
                }
                if (isset($store[$item])) {
                    continue;
                }
                $store[$item] = ['kind' => 'file', 'file' => $itemAbs];
                $p = strrpos($item, '.');
                $ext = ($p === false || $p === 0) ? '' : strtolower(substr($item, $p + 1));
                switch ($ext) {
                    case 'php':
                        if ($state->shortTags !== static::SHORTTAGS_NO) {
                            $newContents = static::expandShortTags($itemAbs, $state->shortTags !== static::SHORTTAGS_KEEPECHO);
                            if ($newContents !== null) {
                                if ($state->updateSourceDirectory) {
                                    if (@file_put_contents($itemAbs, $newContents) === false) {
                                        throw new Exception("Failed to update PHP file $itemAbs");
                                    }
                                } elseif ($state->zip !== null) {
                                    $store[$item] = ['kind' => 'contents', 'contents' => $newContents];
                                }
                                $output->writeln("Expanded short tags in: $dirRel/$item");
                            }
                        }
                        break;
                    case 'pot':
                        if ($dirRel === 'languages') {
                            if ($state->zip !== null && !in_array(static::KEEP_SOURCES, $state->keep)) {
                                $output->writeln("Skipped source file: $dirRel/$item");
                                unset($store[$item]);
                            }
                        }
                        break;
                    case 'po':
                        if (preg_match('%^languages/[^/]+/LC_MESSAGES$%', $dirRel)) {
                            if ($state->zip !== null && !in_array(static::KEEP_SOURCES, $state->keep)) {
                                $output->writeln("Skipped source file: $dirRel/$item");
                                unset($store[$item]);
                            }
                            $compile = false;
                            $compiledAbs = substr($itemAbs, 0, -2) . 'mo';
                            switch ($state->compileTranslations) {
                                case static::YNA_YES:
                                    $compile = true;
                                    break;
                                case static::YNA_AUTO:
                                    if (is_file($compiledAbs)) {
                                        $sourceTime = @filemtime($itemAbs);
                                        $compiledTime = @filemtime($compiledAbs);
                                        if ($sourceTime && $compiledTime && $sourceTime > $compiledTime) {
                                            $compile = true;
                                        }
                                    } else {
                                        $compile = true;
                                    }
                                    break;
                            }
                            if ($compile) {
                                $compiledItem = substr($item, 0, -2) . 'mo';
                                $output->writeln("Compiling language : $dirRel/$item => $dirRel/$compiledItem");
                                $translations = Translations::fromPoFile($itemAbs);
                                if ($state->updateSourceDirectory) {
                                    if ($translations->toMoFile($compiledAbs) === false) {
                                        throw new Exception("Failed to write compiled translations file to $compiledAbs");
                                    }
                                    $store[$compiledItem] = ['kind' => 'file', 'file' => $compiledAbs];
                                } elseif ($state->zip !== null) {
                                    $store[$compiledItem] = ['kind' => 'contents', 'contents' => $translations->toMoString()];
                                }
                            }
                        }
                        break;
                    case 'svg':
                        if ($item === 'icon.svg') {
                            $iconSize = null;
                            if ($dirRel === '') {
                                // Package icon
                                $iconSize = ($state->packageFormat === static::PACKAGEFORMAT_CURRENT) ? ['width' => 200, 'height' => 200] : ['width' => 97, 'height' => 97];
                            } elseif (preg_match('%^blocks/[^/]+$%', $dirRel)) {
                                $iconSize = ($state->packageFormat === static::PACKAGEFORMAT_CURRENT) ? ['width' => 50, 'height' => 50] : ['width' => 16, 'height' => 16];
                            }
                            if ($iconSize !== null) {
                                if ($state->zip !== null && !in_array(static::KEEP_SOURCES, $state->keep)) {
                                    $output->writeln("Skipped source file: $dirRel/$item");
                                    unset($store[$item]);
                                }
                                $compile = false;
                                $compiledAbs = substr($itemAbs, 0, -3) . 'png';
                                switch ($state->compileIcons) {
                                    case static::YNA_YES:
                                        $compile = true;
                                        break;
                                    case static::YNA_AUTO:
                                        if (is_file($compiledAbs)) {
                                            $sourceTime = @filemtime($itemAbs);
                                            $compiledTime = @filemtime($compiledAbs);
                                            if ($sourceTime && $compiledTime && $sourceTime > $compiledTime) {
                                                $compile = true;
                                            }
                                        } else {
                                            $compile = true;
                                        }
                                        break;
                                }
                                if ($compile) {
                                    $compiledItem = substr($item, 0, -3) . 'png';
                                    $output->writeln("Generating png icon: $dirRel/$item => $dirRel/$compiledItem");
                                    $tmpDir = Core::make('helper/file')->getTemporaryDirectory();
                                    if (!is_dir($tmpDir)) {
                                        throw new Exception('Failed to retrieve the temporary directory');
                                    }
                                    $tmpFile = @tempnam($tmpDir, 'c5p');
                                    if ($tmpFile === false) {
                                        throw new Exception('Failed to create a temporary file in directory ' . $tmpDir);
                                    }
                                    try {
                                        $cmd = 'inkscape';
                                        $cmd .= ' --file=' . escapeshellarg($itemAbs);
                                        $cmd .= ' --export-png=' . escapeshellarg($tmpFile);
                                        $cmd .= ' --export-area-page';
                                        $cmd .= ' --export-width=' . $iconSize['width'];
                                        $cmd .= ' --export-height=' . $iconSize['height'];
                                        $cmd .= ' 2>&1';
                                        $execOutput = [];
                                        @exec($cmd, $execOutput, $rc);
                                        if (!is_int($rc)) {
                                            $rc = -1;
                                        }
                                        if ($rc !== 0) {
                                            throw new Exception(implode("\n", $execOutput));
                                        }
                                        $pngData = @file_get_contents($tmpFile);
                                        if ($pngData === false) {
                                            throw new Exception('Failed to read the generated PNG file');
                                        }
                                    } catch (Exception $x) {
                                        @unlink($tmpFile);
                                        throw $x;
                                    }
                                    @unlink($tmpFile);
                                    if ($pngData === '') {
                                        throw new Exception("Inkscape failed to generate the PNG file:\n" . implode("\n", $execOutput));
                                    }
                                    if ($state->updateSourceDirectory) {
                                        if (@file_put_contents($compiledAbs, $pngData) === false) {
                                            throw new Exception("Failed to write rendered SVN icon to $compiledAbs");
                                        }
                                        $store[$compiledItem] = ['kind' => 'file', 'file' => $compiledAbs];
                                    } elseif ($state->zip !== null) {
                                        $store[$compiledItem] = ['kind' => 'contents', 'contents' => $pngData];
                                    }
                                }
                            }
                        }
                        break;
                }
            }
            @closedir($hDir);
            if ($state->zip !== null) {
                foreach ($store as $storeItem => $storeWhat) {
                    $storeName = $storePrefix . '/' . $storeItem;
                    switch ($storeWhat['kind']) {
                        case 'file':
                            if ($state->zip->addFile($storeWhat['file'], $storeName) === false) {
                                $err = "Failed to store file $storeName to zip file";
                                $reason = $state->zip->getStatusString();
                                if ($reason) {
                                    $err .= ": $reason";
                                }
                                throw new Exception($err);
                            }
                            break;
                        case 'contents':
                            if ($state->zip->addFromString($storeName, $storeWhat['contents']) === false) {
                                $err = "Failed to store file $storeName to zip file";
                                $reason = $state->zip->getStatusString();
                                if ($reason) {
                                    $err .= ": $reason";
                                }
                                throw new Exception($err);
                            }
                            break;
                        default:
                            throw new Exception('?');
                    }
                }
            }
            unset($store);
            foreach ($subDirs as $subDir) {
                static::processDirectory($state, $output, $dirAbs . '/' . $subDir, ($dirRel === '') ? $subDir : "$dirRel/$subDir");
            }
        } catch (Exception $x) {
            @closedir($hDir);
            throw $x;
        }
    }

    /**
     * @param string $file
     * @param bool $shortEcho
     *
     * @return string|null
     */
    protected static function expandShortTags($phpFile, $expandShortEcho)
    {
        $phpCode = @file_get_contents($phpFile);
        if ($phpCode === false) {
            throw new Exception("Failed to read contents of file $phpFile");
        }
        $result = '';
        $tokens = @token_get_all($phpCode);
        $numTokens = count($tokens);
        $someExpanded = false;
        for ($i = 0; $i < $numTokens; ++$i) {
            $token = $tokens[$i];
            if (is_array($token)) {
                $expanded = null;
                switch ($token[0]) {
                    case T_OPEN_TAG_WITH_ECHO:
                        if ($expandShortEcho) {
                            $expanded = '<?php echo';
                        }
                        break;
                    case T_OPEN_TAG:
                        $expanded = '<?php';
                        break;
                }
                if ($expanded === null) {
                    $result .= $token[1];
                } else {
                    $someExpanded = true;
                    $result .= $expanded;
                    // Let's see if we have to add some white space after the expanded token
                    if (preg_match('/([ \t\r\n]+)$/', $token[1], $m)) {
                        $result .= $m[1];
                    } elseif ($i < $numTokens - 1 && (!is_array($tokens[$i + 1]) || $tokens[$i + 1][0] !== T_WHITESPACE)) {
                        $result .= ' ';
                    }
                }
            } else {
                $result .= $token;
            }
        }

        return ($someExpanded && $result !== $phpCode) ? $result : null;
    }
}
