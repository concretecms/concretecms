<?php

declare(strict_types=1);

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Support\CodingStyle\PhpFixer;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFileProcessedEvent;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Concrete\Core\Install\Preconditions\PhpVersion;

class PhpCodingStyleCommand extends Command
{
    private const ACTION_CHECK = 'check';
    private const ACTION_FIX = 'fix';

    protected $description = 'Check or fix the PHP coding style.';

    public function __construct($name = null)
    {
        $actionCheck = self::ACTION_CHECK;
        $actionFix = self::ACTION_FIX;
        $defaultWebRoot = str_replace('/', DIRECTORY_SEPARATOR, PhpFixer\Options::getDefaultWebRoot());
        $defaultMinimumPHPVersion = PhpFixer\Options::getDefaultMinimumPHPVersion();
        $this->signature = <<<EOT
c5:phpcs
{--no-cache : Specify this flag to turn off the coding style cache}
{--w|webroot= : Specify the web root directory (if not specified we'll auto-detect it, use - to use {$defaultWebRoot}) }
{--p|php= : The minimum PHP version (if not specified we'll auto-detect it, use - to use {$defaultMinimumPHPVersion}) }
{action : Either "{$actionCheck}" or "{$actionFix}"}
{path*  : The path to one or more files or directories }
EOT
        ;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;

        $this->setHelp(<<<EOT
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
     * @see \Concrete\Core\Console\Command::iniòtialize()
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->canRunAsRoot = $input->getArgument('action') !== 'fix';

        return parent::initialize($input, $output);
    }

    public function handle(PhpFixer $fixer, EventDispatcher $eventDispatcher): int
    {
        $this->checkRequirements();
        $dryRun = $this->getAction() === self::ACTION_CHECK;
        $splFileInfos = $this->getPaths($dryRun);
        $webRoot = $this->getWebRoot($splFileInfos);
        $minimumPHPVersion = $this->getMinimumPHPVersion($webRoot);
        $fixer->getOptions()
            ->setWebRoot($webRoot)
            ->setIsCacheDisabled($this->input->getOption('no-cache'))
            ->setMinimumPhpVersion($minimumPHPVersion)
        ;
        $result = $fixer->fix($this->input, $this->output, $splFileInfos, $dryRun);
        $this->printChanges($result->getChanges(), $dryRun);
        $this->printErrors($result->getErrors(), $dryRun);
        $this->printCountersTable($result->getCounters(), $dryRun);
        if ($dryRun) {
            return $result->getChanges() === [] && $result->getErrors()->isEmpty() ? static::SUCCESS : static::FAILURE;
        }

        return $result->getErrors()->isEmpty() ? static::SUCCESS : static::FAILURE;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function checkRequirements(): void
    {
        if (!interface_exists(FixerInterface::class)) {
            throw new RuntimeException('php-cs-fixer is not installed');
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function getAction(): string
    {
        $action = $this->input->getArgument('action');
        switch ($action) {
            case self::ACTION_CHECK:
            case self::ACTION_FIX:
                return $action;
        }
        throw new UserMessageException(sprintf('Unknown "%s" action: accepted values are "%s" and "%s".', $action, self::ACTION_CHECK, self::ACTION_FIX));
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \SplFileInfo[]
     */
    private function getPaths(bool $dryRun): array
    {
        $splFileInfos = [];
        $uniqueAbsolutePaths = [];
        foreach ($this->input->getArgument('path') as $path) {
            $absolutePath = realpath($path);
            if ($absolutePath === false) {
                throw new UserMessageException(sprintf('The file/directory "%s" could not be found (it is not readable)', $path));
            }
            if (in_array($absolutePath, $uniqueAbsolutePaths, true)) {
                continue;
            }
            $uniqueAbsolutePaths[] = $absolutePath;
            $splFileInfo = new SplFileInfo($absolutePath);
            if ($dryRun === false && !$splFileInfo->isWritable()) {
                throw new UserMessageException(sprintf('The file/directory "%s" is not writable', $path));
            }
            $splFileInfos[] = $splFileInfo;
        }

        return $splFileInfos;
    }

    /**
     * @param \SplFileInfo $paths
     *
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function getWebRoot(array $splFileInfos): string
    {
        $webRoot = (string) $this->input->getOption('webroot');
        if ($webRoot === '-') {
            return PhpFixer\Options::getDefaultWebRoot();
        }
        if ($webRoot === '') {
            return $this->detectWebRoot($splFileInfos);
        }
        $webRootRealPath = realpath($webRoot);
        if ($webRootRealPath === false || !is_dir($webRootRealPath)) {
            throw new UserMessageException(sprintf('The web root directory "%s" does not exist', $webRoot));
        }

        return $webRootRealPath;
    }

    /**
     * @param \SplFileInfo[] $splFileInfos
     *
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function detectWebRoot(array $splFileInfos): string
    {
        $webRoots = [];
        foreach ($splFileInfos as $splFileInfo) {
            $webRootForPath = $this->resolveWebRoot($splFileInfo->isDir() ? $splFileInfo->getPathname() : $splFileInfo->getPath());
            if ($webRootForPath === '') {
                throw new UserMessageException('Unable to detect the web root for ' . $splFileInfo->getPathname());
            }
            if (!in_array($webRootForPath, $webRoots, true)) {
                $webRoots[] = $webRootForPath;
            }
        }
        if (count($webRoots) !== 1) {
            throw new UserMessageException("Multiple web root directories found:\n- " . implode("\n- ", $webRoots));
        }
        return array_shift($webRoots);
    }

    private function resolveWebRoot(string $startingDirectory): string
    {
        $startingDirectory = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $startingDirectory), '/');
        if (preg_match('_^/*$_', $startingDirectory) || preg_match('_^\w:/*$_', $startingDirectory)) {
            return '';
        }
        if (is_file("{$startingDirectory}/index.php") && is_file("{$startingDirectory}/concrete/dispatcher.php")) {
            return $startingDirectory;
        }

        return $this->resolveWebRoot(dirname($startingDirectory));
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function getMinimumPHPVersion(string $webRoot): string
    {
        $minimumPHPVersion = (string) $this->input->getOption('php');
        if ($$minimumPHPVersion === '-') {
            return PhpFixer\Options::getDefaultMinimumPHPVersion();
        }
        if ($minimumPHPVersion === '') {
            return $this->detectMinimumPHPVersion($webRoot);
        }
        return $minimumPHPVersion;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function detectMinimumPHPVersion(string $webRoot): string
    {
        $webRoot = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $webRoot), '/');
        // Since Concrete 8.3.0: read the Concrete\Core\Install\Preconditions\PhpVersion::MINIMUM_PHP_VERSION constant
        $file = "{$webRoot}/concrete/src/Install/Preconditions/PhpVersion.php";
        if (is_file($file)) {
            return $this->detectMinimumPHPVersionFromInstallPrecondition($file);
        }
        // Since Concrete 5.7.0: extract the Concrete version from concrete/config/concrete.php and calculate the PHP version from it
        $file = "{$webRoot}/concrete/config/concrete.php";
        if (is_file($file)) {
            return $this->detectMinimumPHPVersionFromConcreteVersion($this->extractConcreteVersionFromConfig($file));
        }
        // Since Concrete 5.3.3.2b3: extract the Concrete version from /concrete/config/version.php and calculate the PHP version from it
        $file = "{$webRoot}/concrete/config/version.php";
        if (is_file($file)) {
            return $this->detectMinimumPHPVersionFromConcreteVersion($this->extractConcreteVersionFromVersionFile($file));
        }
        throw new UserMessageException(sprintf('Failed to detect the minimum PHP version for the web root "%s"', str_replace('/', DIRECTORY_SEPARATOR, $webRoot)));
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @see https://github.com/concrete5/concrete5/blob/8.5.6/concrete/src/Install/Preconditions/PhpVersion.php#L15
     */
    private function detectMinimumPHPVersionFromInstallPrecondition(string $file): string
    {
        $tokens = $this->getRelevantPhpTokens($file);
        $nestingLevel = null;
        $maxTokenIndex = count($tokens) - 5; // 'MINIMUM_PHP_VERSION' + '=' + ConstantValue + ';' + '}'
        for ($tokenIndex = 0; $tokenIndex < $maxTokenIndex; $tokenIndex++) {
            $token = $tokens[$tokenIndex];
            if ($nestingLevel === null) {
                if (is_array($token) && $token[0] === T_CLASS) {
                    $nestingLevel = 0;
                }
            } elseif ($token === '{') {
                $nestingLevel++;
            } elseif ($token === '}') {
                $nestingLevel--;
            } elseif ($nestingLevel === 1) {
                if (is_array($token) && $token[0] === T_CONST) {
                    if (is_array($tokens[$tokenIndex + 1]) && $tokens[$tokenIndex + 1][0] === T_STRING && $tokens[$tokenIndex + 1][1] === 'MINIMUM_PHP_VERSION') {
                        if ($tokens[$tokenIndex + 2] === '=') {
                            if (is_array($tokens[$tokenIndex + 3]) && $tokens[$tokenIndex + 3][0] === T_CONSTANT_ENCAPSED_STRING) {
                                return substr($tokens[$tokenIndex + 3][1], 1, -1);
                            }
                        }
                    }
                }
            }
        }
        throw new UserMessageException(sprintf('Failed to detect the minimum PHP version from the file %s', str_replace('/', DIRECTORY_SEPARATOR, $file)));
    }
    
    private function detectMinimumPHPVersionFromConcreteVersion(string $concreteVersion): string
    {
        if (version_compare($concreteVersion, '9') >= 0) {
            return PhpVersion::MINIMUM_PHP_VERSION; // 7.3
        }
        if (version_compare($concreteVersion, '8') >= 0) {
            return '5.5.9';
        }
        if (version_compare($concreteVersion, '5.7') >= 0) {
            return '5.3.3';
        }
        if (version_compare($concreteVersion, '5.6.4') >= 0) {
            return '5.3.2';
        }
        return '5.2.4';
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @see https://github.com/concrete5/concrete5/blob/8.5.6/concrete/config/concrete.php#L9
     */
    private function extractConcreteVersionFromConfig(string $file): string
    {
        $tokens = $this->getRelevantPhpTokens($file);
        $arrayFound = false;
        $maxTokenIndex = count($tokens) - 5; // 'version' + '=>' + VersionValue + ')' + ';'
        for ($tokenIndex = 0; $tokenIndex < $maxTokenIndex; $tokenIndex++) {
            $token = $tokens[$tokenIndex];
            if ($arrayFound === false) {
                if (is_array($token) && $token[0] === T_ARRAY && $tokens[$tokenIndex + 1] === '(') {
                    $arrayFound = true;
                    $tokenIndex++;
                }
                continue;
            }
            if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING && preg_match('/^.version.$/', $token[1])) {
                if (is_array($tokens[$tokenIndex + 1]) && $tokens[$tokenIndex + 1][0] === T_DOUBLE_ARROW) {
                    if (is_array($tokens[$tokenIndex + 2]) && $tokens[$tokenIndex + 2][0] === T_CONSTANT_ENCAPSED_STRING) {
                        return substr($tokens[$tokenIndex + 2][1], 1, -1);
                    }
                }
            }
        }
        throw new UserMessageException(sprintf('Failed to detect the Concrete version from the file %s', str_replace('/', DIRECTORY_SEPARATOR, $file)));
    }
    
    /**
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @see https://github.com/concrete5/concrete5-legacy/blob/5.6.4.0/web/concrete/config/version.php#L3
     * @see https://github.com/concrete5/concrete5-legacy/blob/5.4.2/web/concrete/config/version.php#L3
     */
    private function extractConcreteVersionFromVersionFile(string $file): string
    {
        $tokens = $this->getRelevantPhpTokens($file);
        foreach (array_filter($tokens, 'is_array') as $i => $token) {
            //$tokens[$i][0] = token_name($token[0]);
        }
        $maxTokenIndex = count($tokens) - 3; // '$APP_VERSION' + '=' + VersionValue + ';'
        for ($tokenIndex = 0; $tokenIndex < $maxTokenIndex; $tokenIndex++) {
            $token = $tokens[$tokenIndex];
            if (is_array($token) && $token[0] === T_VARIABLE && $token[1] === '$APP_VERSION') {
                if ($tokens[$tokenIndex + 1] === '=') {
                    if (is_array($tokens[$tokenIndex + 2]) && $tokens[$tokenIndex + 2][0] === T_CONSTANT_ENCAPSED_STRING) {
                        return substr($tokens[$tokenIndex + 2][1], 1, -1);
                    }
                }
            }
        }
        throw new UserMessageException(sprintf('Failed to detect the Concrete version from the file %s', str_replace('/', DIRECTORY_SEPARATOR, $file)));
    }

    private function printChanges(array $changes, bool $dryRun): void
    {
        if (!$dryRun) {
            return;
        }
        foreach ($changes as $file => $change) {
            $this->output->writeln(sprintf('### CHANGES REQUIRED IN FILE %s ###', $file));
            $this->output->writeln($change['diff']);
        }
    }

    private function printErrors(ErrorsManager $errors, bool $dryRun): void
    {
        foreach ([
            'LINTING ERRORS' => $errors->getInvalidErrors(),
            'FIXER ERRORS' => $errors->getExceptionErrors(),
            'POST-PROCESSING ERRORS' => $errors->getLintErrors(),
        ] as $name => $list) {
            if ($list === []) {
                continue;
            }
            $this->output->writeln(sprintf('<error>### %s ###</error>', $name));
            foreach ($list as $index => $error) {
                $this->output->writeln(sprintf('<error>%s. File %s</error>', $index + 1, $error->getFilePath()));
                $exception = $error->getSource();
                if ($exception) {
                    $lines = explode("\n", str_replace("\r\n", "\n", trim($exception->getMessage())));
                    foreach ($lines as $line) {
                        $this->output->writeln(sprintf('<error>   %s</error>', $line));
                    }
                }
            }
            $this->output->writeln('');
        }
    }

    private function printCountersTable(array $counters, bool $dryRun): void
    {
        $rows = [];
        $headerLength = 0;
        $valueLength = 0;
        ksort($counters);
        foreach ($counters as $state => $count) {
            $count = number_format($count, 0, '.', ',');
            switch ($state) {
                case FixerFileProcessedEvent::STATUS_INVALID:
                    $header = 'Files ignored for syntax errors';
                    break;
                    break;
                case FixerFileProcessedEvent::STATUS_SKIPPED:
                    $header = 'Skipped files';
                    break;
                case FixerFileProcessedEvent::STATUS_NO_CHANGES:
                    $header = $dryRun ? 'Files already fixed' : 'Unchanged files';
                    break;
                case FixerFileProcessedEvent::STATUS_FIXED:
                    $header = $dryRun ? 'Files that should be fixed' : 'Fixed files';
                    break;
                case FixerFileProcessedEvent::STATUS_EXCEPTION:
                    $header = 'Fixer exceptions';
                    break;
                case FixerFileProcessedEvent::STATUS_LINT:
                    $header = 'Linter errors';
                    break;
                case FixerFileProcessedEvent::STATUS_UNKNOWN:
                default:
                    $header = 'unknown';
                    break;
            }
            $rows[] = [$header, $count];
            $headerLength = max($headerLength, strlen($header));
            $valueLength = max($valueLength, strlen($count));
        }
        if ($rows !== []) {
            $this->output->writeln('Summary:');
            foreach ($rows as $row) {
                $this->output->writeln('- ' . str_pad($row[0], $headerLength, ' ', STR_PAD_RIGHT) . ': ' . str_pad($row[1], $valueLength, ' ', STR_PAD_LEFT));
            }
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function getRelevantPhpTokens(string $file): array
    {
        $fileContents = file_get_contents($file);
        if (!is_string($fileContents)) {
            throw new UserMessageException('Failed to read the file ' . str_replace('/', DIRECTORY_SEPARATOR, $file));
        }
        $allTokens = token_get_all($fileContents);
        $relevantTokens= array_filter(
            $allTokens,
            static function($token): bool {
                return !is_array($token) || !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true);
            }
        );

        return array_values($relevantTokens);
    }
}
