<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle;

use DirectoryIterator;
use PhpCsFixer\Console\Output\ProcessOutput;
use PhpCsFixer\FixerFileProcessedEvent;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

class PhpFixer
{
    /**
     * Coding style rules flag: no flags.
     *
     * @var int
     */
    public const FLAG_NONE = 0b00000000;

    /**
     * Coding style rules flag: compatible with very old PHP versions.
     *
     * @var int
     */
    public const FLAG_OLDPHP = 0b00000001;

    /**
     * Coding style rules flag: PHP-only files.
     *
     * @var int
     */
    public const FLAG_PHPONLY = 0b00000010;

    /**
     * Coding style rules flag: bootstrap files.
     *
     * @var int
     */
    public const FLAG_BOOTSTRAP = self::FLAG_OLDPHP | self::FLAG_PHPONLY;

    /**
     * Coding style rules flag: files that implement classes following the PSR-4 rules.
     *
     * @var int
     */
    public const FLAG_PSR4CLASS = 0b00000100 + self::FLAG_PHPONLY;

    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixer\Options
     */
    protected $options;

    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixer\RuleResolver
     */
    protected $ruleResolver;

    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixer\Runner
     */
    protected $runner;

    /**
     * Initialize the instance.
     */
    public function __construct(PhpFixer\Options $options, PhpFixer\RuleResolver $ruleResolver, PhpFixer\Runner $runner)
    {
        $this->options = $options;
        $this->ruleResolver = $ruleResolver;
        $this->runner = $runner;
    }

    /**
     * Get the fixer options.
     */
    public function getOptions(): PhpFixer\Options
    {
        return $this->options;
    }

    /**
     * @param \SplFileInfo[] $paths with absolute paths
     */
    public function fix(InputInterface $input, OutputInterface $output, array $paths, $dryRun = false): PhpFixer\Result
    {
        $pathRuleList = [];
        foreach ($paths as $path) {
            $pathRuleList = $this->mergePathsAndFlags($pathRuleList, $this->splitPathToPathAndFlags($path));
        }
        $this->runner->resetSteps();
        foreach ($pathRuleList as $flags => $paths) {
            $this->runner->addStep($this->options, $paths, $flags);
        }

        $progressOutput = new ProcessOutput(
            $output,
            $this->runner->getEventDispatcher(),
            (new Terminal())->getWidth(),
            $this->runner->calculateNumberOfFiles(),
        );
        $counters = [];
        $counter = static function(FixerFileProcessedEvent $e) use (&$counters) {
            $status = $e->getStatus();
            if (isset($counters[$status])) {
                $counters[$status]++;
            } else {
                $counters[$status] = 1;
            }
        };
        $this->runner->getEventDispatcher()->addListener(FixerFileProcessedEvent::NAME, $counter);
        try {
            $progressOutput->printLegend();
            $changes = $this->runner->apply($dryRun);
            $output->writeln('');
        } finally {
            $this->runner->getEventDispatcher()->removeListener(FixerFileProcessedEvent::NAME, $counter);
        }

        return new PhpFixer\Result($counters, $changes, clone $this->runner->getErrorsManager());
    }

    /**
     * @throws \RuntimeException if $path could not be found
     */
    protected function splitPathToPathAndFlags(SplFileInfo $path): array
    {
        if ($path->isFile()) {
            return $this->splitFileToPathAndFlags($path);
        }
        if ($path->isDir()) {
            return $this->splitDirectoryToPathAndFlags($path);
        }
        throw new RuntimeException(t('Failed to find the file/directory %s', $path->getPathname()));
    }

    protected function splitFileToPathAndFlags(SplFileInfo $file): array
    {
        if (strpos($file->getFilename(), '.') === 0) {
            // Exclude dot files
            return [];
        }
        $isExtensionOk = in_array(mb_strtolower($file->getExtension()), $this->options->getFilterByExtensions());
        $fullPath = $this->options->normalizePath($file->getPathname(), false, false);
        if (strpos($fullPath, $this->options->getWebRoot()) !== 0) {
            // Outside the webroot: we don't know the exact rules.
            return $isExtensionOk ? [self::FLAG_NONE => [$fullPath]] : [];
        }
        $relativePath = substr($fullPath, strlen($this->options->getWebRoot()));
        if ($isExtensionOk === false && !in_array($relativePath, $this->options->getFilterIncludeFiles())) {
            // Let's skip this file, since it doesn't have the allowed file extensions and it's not in the whitelist
            return [];
        }
        foreach ($this->options->getIgnoredDirectoriesByPath() as $check) {
            if (strpos($relativePath, $check) === 0) {
                // The file is in a directory marked as to be ignored: no operation should be done
                return [];
            }
        }
        if (in_array($relativePath, $this->options->getBootstrapFiles(), true)) {
            // The file is a bootstrap file
            return [self::FLAG_BOOTSTRAP => [$fullPath]];
        }
        foreach ($this->options->getPhpOnlyNonPsr4Regexes() as $pattern) {
            if (preg_match($pattern, $relativePath)) {
                // The file is a PHP-only file that don't follow PSR-4 class name rules.
                return [self::FLAG_PHPONLY => [$fullPath]];
            }
        }
        foreach ($this->options->getPhpOnlyPsr4Regexes() as $pattern) {
            if (preg_match($pattern, $relativePath)) {
                // The file is a PHP-only file that follow PSR-4 class name rules.
                return [self::FLAG_PSR4CLASS => [$fullPath]];
            }
        }
        // No specific rules found.
        return [self::FLAG_NONE => [$fullPath]];
    }

    protected function splitDirectoryToPathAndFlags(SplFileInfo $directory): array
    {
        if (strpos($directory->getFilename(), '.') === 0) {
            // Exclude dot directories
            return [];
        }
        if (in_array($directory->getFilename(), $this->options->getIgnoredDirectoriesByName(), true)) {
            // Explicitly excluded directories
            return [];
        }
        $fullPath = $this->options->normalizePath($directory->getPathname(), true, false);
        if (strpos($fullPath, $this->options->getWebRoot()) !== 0) {
            // Outside the webroot: we don't know the exact rules.
            return [self::FLAG_NONE => [$fullPath]];
        }
        $relativePath = substr($fullPath, strlen($this->options->getWebRoot()));
        if ($relativePath === '') {
            $relativePath = '/';
        }
        if ($this->options->isDirectoryWithMixedContents($relativePath)) {
            // The directory contains files with mixed rules: we have to parse the directory contents.
            $result = [];
            $iterator = new DirectoryIterator($fullPath);
            foreach ($iterator as $child) {
                if (!$child->isDot()) {
                    $result = $this->mergePathsAndFlags($result, $this->splitPathToPathAndFlags($child));
                }
            }

            return $result;
        }

        foreach ($this->options->getIgnoredDirectoriesByPath() as $check) {
            if (strpos($relativePath, $check) === 0) {
                // The directory is (inside) a directory marked as to be ignored: no operation should be done
                return [];
            }
        }
        foreach ($this->options->getPhpOnlyNonPsr4Regexes() as $pattern) {
            if (preg_match($pattern, $relativePath)) {
                // The directory contains PHP-only files that don't PSR-4 class name rules.
                return [self::FLAG_PHPONLY => [$fullPath]];
            }
        }
        foreach ($this->options->getPhpOnlyPsr4Regexes() as $pattern) {
            if (preg_match($pattern, $relativePath)) {
                // The directory contains PHP-only files that follow PSR-4 class name rules.
                return [self::FLAG_PSR4CLASS => [$fullPath]];
            }
        }
        // No specific rules found.
        return [self::FLAG_NONE => [$fullPath]];
    }

    protected function mergePathsAndFlags(array $list1, array $list2): array
    {
        foreach ($list2 as $key => $paths) {
            if (isset($list1[$key])) {
                $list1[$key] = array_values(array_merge($list1[$key], $paths));
            } else {
                $list1[$key] = $paths;
            }
        }

        return $list1;
    }
}
