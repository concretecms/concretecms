<?php

namespace Concrete\Core\Support\CodingStyle;

use DirectoryIterator;
use PhpCsFixer\Console\Output\ProcessOutput;
use PhpCsFixer\FixerFileProcessedEvent;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpFixer
{
    /**
     * Coding style rules flag: compatible with PHP versions prior to 5.5.9 (the minimum PHP version required by concrete5).
     *
     * @var int
     */
    const FLAG_OLDPHP = 0x01;

    /**
     * Coding style rules flag: PHP-only files.
     *
     * @var int
     */
    const FLAG_PHPONLY = 0x02;

    /**
     * Coding style rules flag: bootstrap files.
     *
     * @var int
     */
    const FLAG_BOOTSTRAP = 0x03; // FLAG_OLDPHP | FLAG_PHPONLY

    /**
     * Coding style rules flag: files that implement classes following the PSR-4 rules.
     *
     * @var int
     */
    const FLAG_PSR4CLASS = 0x06; // Includes FLAG_PHPONLY

    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixerOptions
     */
    protected $options;

    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixerRuleResolver
     */
    protected $ruleResolver;

    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixerRunner
     */
    protected $runner;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerOptions $options
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerRuleResolver $ruleResolver
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerRunner $runner
     */
    public function __construct(PhpFixerOptions $options, PhpFixerRuleResolver $ruleResolver, PhpFixerRunner $runner)
    {
        $this->options = $options;
        $this->ruleResolver = $ruleResolver;
        $this->runner = $runner;
    }

    /**
     * Get the fixer options.
     *
     * @return \Concrete\Core\Support\CodingStyle\PhpFixerOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \SplFileInfo[] $paths with absolute paths
     * @param bool $dryRun
     *
     * @return array
     */
    public function fix(InputInterface $input, OutputInterface $output, array $paths, $dryRun = false)
    {
        $pathRuleList = [];
        foreach ($paths as $path) {
            $pathRuleList = $this->mergePathAndFlags($pathRuleList, $this->splitPathToPathAndFlags($path));
        }
        $this->runner->resetSteps();
        foreach ($pathRuleList as $flags => $paths) {
            $this->runner->addStep($this->options, $paths, $flags);
        }

        $progressOutput = new ProcessOutput($output, $this->runner->getEventDispatcher()->getEventDispatcher(), null, $this->runner->calculateNumberOfFiles());
        $counters = [];
        $counter = function (FixerFileProcessedEvent $e) use (&$counters) {
            $status = $e->getStatus();
            if (isset($counters[$status])) {
                ++$counters[$status];
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

        return [$counters, $changes, clone $this->runner->getErrorManager()];
    }

    /**
     * @param \SplFileInfo $path
     *
     * @throws \RuntimeException if $path could not be found
     *
     * @return array
     */
    protected function splitPathToPathAndFlags(SplFileInfo $path)
    {
        if ($path->isFile()) {
            return $this->splitFileToPathAndFlags($path);
        }
        if ($path->isDir()) {
            return $this->splitDirectoryToPathAndFlags($path);
        }
        throw new RuntimeException(t('Failed to find the file/directory %s', $path->getPathname()));
    }

    /**
     * @param \SplFileInfo $file
     *
     * @return array
     */
    protected function splitFileToPathAndFlags(SplFileInfo $file)
    {
        if (strpos($file->getFilename(), '.') === 0) {
            // Exclude dot files
            return [];
        }
        $isExtensionOk = in_array(mb_strtolower($file->getExtension()), $this->options->getFilterByExtensions());
        $fullPath = $this->options->normalizePath($file->getPathname(), false, false);
        if (strpos($fullPath, $this->options->getWebRoot()) !== 0) {
            // Outside the webroot: we don't know the exact rules.
            return $isExtensionOk ? [0 => [$fullPath]] : [];
        }
        $relativePath = substr($fullPath, strlen($this->options->getWebRoot()));
        if ($isExtensionOk === false && !in_array($relativePath, $this->options->getFilterIncludeFiles())) {
            // Let's skip this file, since it doesn't have the allowed file extensions and it's not in the allowlist
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
        return [0 => [$fullPath]];
    }

    /**
     * @param \SplFileInfo $directory
     *
     * @return array
     */
    protected function splitDirectoryToPathAndFlags(SplFileInfo $directory)
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
            return [0 => [$fullPath]];
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
                    $result = $this->mergePathAndFlags($result, $this->splitPathToPathAndFlags($child));
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
        return [0 => [$fullPath]];
    }

    /**
     * @param array $list1
     * @param array $list2
     *
     * @return array
     */
    protected function mergePathAndFlags($list1, $list2)
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
