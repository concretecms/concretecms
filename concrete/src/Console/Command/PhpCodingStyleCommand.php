<?php

declare(strict_types=1);

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Service\File as FileService;
use Concrete\Core\Install\Preconditions\PhpVersion;
use Concrete\Core\Support\CodingStyle\PHPCSFixerConfigurator;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

defined('C5_EXECUTE') or die('Access Denied.');

final class PhpCodingStyleCommand extends Command
{
    private const DEFAULT_WEBROOT = DIR_BASE;

    private const DEFAULT_PHPCSFIXER_PATH = 'php-cs-fixer';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Console\Command::$description
     */
    protected $description = 'Check or fix the PHP coding style.';

    /**
     * @var \Concrete\Core\File\Service\File
     */
    private $fileService;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $fileSystem;

    public function __construct($name = null)
    {
        $defaultWebRoot = self::DEFAULT_WEBROOT;
        $defaultPhpCsFixerPath = self::DEFAULT_PHPCSFIXER_PATH;
        $defaultMinimumPHPVersion = PhpVersion::MINIMUM_PHP_VERSION;

        $this->signature = <<<EOT
        c5:phpcs
        {--webroot={$defaultWebRoot} : Specify the webroot (use - to auto-detect it)}
        {--p|php={$defaultMinimumPHPVersion} : The minimum PHP version }
        {--pcfpath={$defaultPhpCsFixerPath} : Specify the path of PHP CS Fixer executable}
        {--no-cache : Specify this flag to turn off the coding style cache}
        {--1 : Stop after the first violation is met}
        {action : Either "fix" or "check"}
        {path*  : The path to one or more files or directories }
        EOT;
        parent::__construct($name);
    }

    public function handle(FileService $fileService, FileSystem $fileSystem): int
    {
        $configFile = null;
        try {
            $this->fileService = $fileService;
            $this->fileSystem = $fileSystem;
            $dryRun = $this->resolveDryRun();
            $phpCsFixerQuotedPath = $this->resolvePhpCsFixerQuotedPath();
            $minimumPHPVersion = $this->resolveMinimumPHPVersion();
            $this->output->writeln("Fixing using rules for PHP {$minimumPHPVersion}");
            $paths = $this->resolveFiles($dryRun);
            $webroot = $this->resolveWebRoot($paths);
            $configFile = $this->createConfigFile($webroot, $minimumPHPVersion);

            return $this->launchPhpCsFixer($phpCsFixerQuotedPath, $dryRun, $configFile, $webroot, $paths);
        } catch (UserMessageException $x) {
            $this->output->error($x->getMessage());

            return self::FAILURE;
        } finally {
            if ($configFile !== null) {
                $this->fileSystem->delete($configFile);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure(): void
    {
        $okExitCode = self::SUCCESS;
        $errExitCode = self::FAILURE;

        $this->setHelp(
            <<<EOT
            Check or fix the PHP coding style.
            
            Return values when checking the coding style:
            - {$okExitCode}: the coding style is valid and no error occurred
            - {$errExitCode}: some fixes are needed or some error occurred
            
            Return values when applying the coding style:
            - {$okExitCode}: no error occurred
            - {$errExitCode}: some error occurred
            EOT
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Console\Command::initialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->canRunAsRoot = $input->getArgument('action') !== 'fix';
        parent::initialize($input, $output);
    }

    private function resolvePhpCsFixerQuotedPath(): string
    {
        $raw = $this->input->getOption('pcfpath') ?: self::DEFAULT_PHPCSFIXER_PATH;
        $raw = str_replace('/', DIRECTORY_SEPARATOR, $raw);
        $path = realpath($raw);
        if ($path !== false && !is_file($path)) {
            $path = false;
        }
        if ($path === false && !str_contains($raw, DIRECTORY_SEPARATOR)) {
            $rc = -1;
            $output = [];
            if (DIRECTORY_SEPARATOR === '\\') {
                exec('where.exe ' . $this->escapeShellArg($raw) . ' 2>NUL', $output, $rc);
            } else {
                exec('which ' . $this->escapeShellArg($raw) . ' 2>/dev/null', $output, $rc);
            }
            if ($rc === 0) {
                foreach ($output as $line) {
                    $resolved = realpath($line);
                    if ($resolved !== false && is_file($resolved)) {
                        $path = $resolved;
                        break;
                    }
                }
            }
        }
        if ($path === false) {
            throw new UserMessageException("Failed resolve PHP CS Fixer from '{$raw}'");
        }
        $quotedPath = $this->escapeShellArg($path);
        if ($this->isPHPFile($path)) {
            $phpParams = '';
            if (function_exists('xdebug_break')) {
                $phpParams .= '-d xdebug.mode=off -d xdebug.default_enable=0 -d xdebug.remote_autostart=0 -d xdebug.remote_enable=0 -d xdebug.remote_host=unix:///dev/null -d xdebug.profiler_enable=0';
            }
            $quotedPath = $this->escapeShellArg(PHP_BINARY) . ' ' . $phpParams . ' ' . $quotedPath;
        }
        $rc = -1;
        $output = [];
        exec("{$quotedPath} --version 2>&1", $output, $rc);
        if ($rc !== 0) {
            throw new UserMessageException("Failed to check PHP CS Fixer:\n" . trim(implode("\n", $output)));
        }
        $output = implode("\n", $output);
        if (!preg_match('/^PHP CS Fixer \d/m', $output)) {
            throw new UserMessageException("Unexpected output from PHP CS Fixer:\n" . trim($output));
        }

        return $quotedPath;
    }

    private function resolveMinimumPHPVersion(): string
    {
        $minimumPHPVersion = $this->input->getOption('php');
        if ($minimumPHPVersion === '') {
            $minimumPHPVersion = PhpVersion::MINIMUM_PHP_VERSION;
        }
        try {
            return PHPCSFixerConfigurator::parseMinimumPHPVersionFormat($minimumPHPVersion);
        } catch (\RuntimeException $x) {
            throw new UserMessageException($x->getMessage());
        }
    }

    private function resolveDryRun(): bool
    {
        $action = $this->input->getArgument('action');
        switch ($action) {
            case 'fix':
                return false;
            case 'check':
                return true;
                break;
            default:
                throw new UserMessageException("Unknown '{$action}' action: accepted values are 'fix' and 'check'.");
        }
    }

    /**
     * @return \SplFileInfo[]
     */
    private function resolveFiles(bool $dryRun): array
    {
        $paths = [];
        foreach ($this->input->getArgument('path') as $path) {
            $absolutePath = realpath($path);
            if ($absolutePath === false) {
                throw new UserMessageException("The file/directory '{$path}' could not be found (it is not readable)");
            }
            $path = new \SplFileInfo($absolutePath);
            if ($dryRun === false && !$path->isWritable()) {
                throw new UserMessageException("The file/directory '{$path}' is not writable");
            }
            $paths[] = $path;
        }

        return $paths;
    }

    /**
     * @param \SplFileInfo[] $paths
     */
    private function resolveWebRoot(array $paths): string
    {
        $webroot = (string) $this->input->getOption('webroot');
        if ($webroot !== '' && $webroot === '-') {
            return $this->resolveWebRootFromStartingPoint($paths[0]->getPathname());
        }

        return self::DEFAULT_WEBROOT;
    }

    private function resolveWebRootFromStartingPoint(string $startingPoint): string
    {
        if (is_file($startingPoint)) {
            $startingPoint = dirname($startingPoint);
        }
        $startingPoint = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $startingPoint), '/');
        if (!str_contains($startingPoint, '/')) {
            throw new UserMessageException('Unable to detect the webroot');
        }
        if (is_file("{$startingPoint}/index.php") && is_file("{$startingPoint}/concrete/dispatcher.php")) {
            return $startingPoint;
        }
        if (preg_match('_^/+$_', $startingPoint) || preg_match('_^\w:/*$_', $startingPoint)) {
            throw new UserMessageException('Unable to detect the webroot');
        }

        return $this->resolveWebRootFromStartingPoint(dirname($startingPoint));
    }

    private function createConfigFileContent(string $webroot, string $minimumPHPVersion): string
    {
        $dirBase = "'" . addcslashes($webroot, "'\\") . "'";
        $minimumPHPVersion = "'" . addcslashes($minimumPHPVersion, "'\\") . "'";

        return <<<EOT
        <?php
        use Concrete\Core\Support\CodingStyle\PHPCSFixerConfigurator;
        const DIR_BASE = {$dirBase};
        require_once DIR_BASE . '/concrete/bootstrap/configure.php';
        require_once DIR_BASE_CORE . '/src/Support/CodingStyle/autoload.php';
        try {
            return (new PHPCSFixerConfigurator({$minimumPHPVersion}))
                ->setEnvironmentVariables()
                ->createConfig()
            ;
        } catch (RuntimeException \$x) {
            fwrite(STDERR, trim(\$x->getMessage()) . "\\n");
            exit(1);
        }
        EOT;
    }

    private function createConfigFile(string $webroot, string $minimumPHPVersion): string
    {
        $contents = $this->createConfigFileContent($webroot, $minimumPHPVersion);
        $tempDir = $this->fileService->getTemporaryDirectory();
        $tempFile = tempnam($tempDir, 'ccm');
        if (!$tempFile) {
            throw new UserMessageException('Failed to create a temporary file');
        }
        $tempFile = str_replace(DIRECTORY_SEPARATOR, '/', $tempFile);
        $delete = true;
        try {
            if (!$this->fileSystem->put($tempFile, $contents)) {
                throw new UserMessageException('Failed to set the content of a temporary file');
            }
            $delete = false;
        } finally {
            if ($delete) {
                $this->fileSystem->delete($tempFile);
            }
        }

        return $tempFile;
    }

    /**
     * @param \SplFileInfo[] $paths
     */
    private function launchPhpCsFixer(string $phpCsFixerQuotedPath, bool $dryRun, string $configFile, string $webroot, array $paths): int
    {
        $commandLine = "{$phpCsFixerQuotedPath} fix";
        $commandLine .= $this->output->isDecorated() ? ' --ansi' : ' --no-ansi';
        if (!$this->input->isInteractive()) {
            $commandLine .= ' --no-interaction';
        }
        if ($dryRun) {
            $commandLine .= ' --dry-run';
        }
        if ($dryRun || $this->output->isVerbose()) {
            $commandLine .= ' --diff';
        }
        if ($this->output->isDebug()) {
            $commandLine .= ' -vvv';
        } elseif ($this->output->isVeryVerbose()) {
            $commandLine .= ' -vv';
        } elseif ($this->output->isVerbose()) {
            $commandLine .= ' -v';
        }
        if ($this->input->getOption('no-cache')) {
            $commandLine .= ' --using-cache=no';
        }
        if ($this->input->getOption('1')) {
            $commandLine .= ' --stop-on-violation';
        }
        $commandLine .= ' ' . $this->escapeShellArg("--config={$configFile}");
        $commandLine .= ' --';
        foreach ($this->resolveRelativePaths($webroot, $paths) as $relativePath) {
            $commandLine .= ' ' . $this->escapeShellArg($relativePath);
        }
        $previousWorkingDirectory = getcwd();
        if ($previousWorkingDirectory === false) {
            throw new UsermessageException('Failed to get the current working directory');
        }
        if (!chdir($webroot)) {
            throw new UsermessageException("Failed to set the current working directory to {$webroot}");
        }
        try {
            $rc = -1;
            if (DIRECTORY_SEPARATOR === '\\') {
                passthru("start /b cmd /c {$commandLine}", $rc);
            } else {
                passthru("{$commandLine} &", $rc);
            }
        } finally {
            chdir($previousWorkingDirectory);
        }

        return $rc;
    }

    /**
     * @param \SplFileInfo[] $paths
     *
     * @return \Generator<string>
     */
    private function resolveRelativePaths(string $webroot, array $paths): \Generator
    {
        $stripPrefix = $webroot . '/';
        $stripPrefixLength = strlen($stripPrefix);

        foreach ($paths as $path) {
            $fullpath = str_replace(DIRECTORY_SEPARATOR, '/', $path->getPathname());

            yield str_starts_with($fullpath, $stripPrefix) ? ('./' . substr($fullpath, $stripPrefixLength)) : $fullpath;
        }
    }

    private function escapeShellArg(string $s): string
    {
        $safe = '\w:/.=\-';
        if (DIRECTORY_SEPARATOR === '\\') {
            $safe .= ',\\\\';
        }

        return preg_match('#^[' . $safe . ']+$#D', $s) ? $s : escapeshellarg($s);
    }

    private function isPHPFile(string $path): bool
    {
        if (!$this->fileSystem->isReadable($path) || !$this->fileSystem->isFile($path)) {
            return false;
        }
        try {
            foreach ($this->fileSystem->lines($path) as $line) {
                return stripos($line, '<?php') !== false || preg_match('{^#!(/usr)?/bin/.*\\bphp\\b}', $line);
            }
        } catch (\Throwable $_) {
        }

        return false;
    }
}
