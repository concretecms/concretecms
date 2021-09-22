<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle\PhpFixer;

use ArrayObject;
use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\FileCacheManager;
use PhpCsFixer\Cache\FileHandler;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Cache\Signature;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Runner\Runner as PhpCsFixerRunner;
use PhpCsFixer\ToolInfo;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Traversable;

/**
 * Wrapper to PHP-CS-Fixer.
 */
class Runner
{
    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixer\RuleResolver
     */
    protected $ruleResolver;

    /**
     * @var array
     */
    protected $steps;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher|null
     */
    private $eventDispatcher;

    /**
     * @var \PhpCsFixer\Error\ErrorsManager|null
     */
    private $errorsManager;

    /**
     * @var string|null
     */
    private $phpCSFixerVersion;

    public function __construct(RuleResolver $ruleResolver, EventDispatcher $eventDispatcher)
    {
        $this->ruleResolver = $ruleResolver;
        $this->resetSteps();
        $this->resetErrors();
    }

    public function getEventDispatcher(): EventDispatcher
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher();
        }

        return $this->eventDispatcher;
    }

    /**
     * @param string[] $paths
     *
     * @return $this
     */
    public function addStep(Options $options, array $paths, int $flags): self
    {
        $this->steps[] = [
            'options' => $options,
            'finder' => $this->createFinder($options, $paths),
            'flags' => $flags,
        ];

        return $this;
    }

    /**
     * @return $this
     */
    public function resetSteps(): self
    {
        $this->steps = [];

        return $this;
    }

    public function getErrorsManager(): ErrorsManager
    {
        if ($this->errorsManager === null) {
            $this->errorsManager = new ErrorsManager();
        }

        return $this->errorsManager;
    }

    public function calculateNumberOfFiles(): int
    {
        $sum = 0;
        foreach (array_keys($this->steps) as $stepIndex) {
            $this->expandFinder($stepIndex);
            $sum += count($this->steps[$stepIndex]['finder']);
        }

        return $sum;
    }

    public function apply(bool $dryRun): array
    {
        $this->resetErrors();
        $changes = [];
        foreach ($this->steps as $step) {
            $changes += $this->applyStep($step['options'], $step['finder'], $step['flags'], $dryRun);
        }

        return $changes;
    }

    protected function applyStep(Options $options, Traversable $finder, int $flags, bool $dryRun): array
    {
        $rules = $this->ruleResolver->getRules($flags, true, $options->getMinimumPhpVersion());
        $fixers = $this->ruleResolver->getFixers($rules);

        $linter = new Linter();
        $runner = new PhpCsFixerRunner(
            $finder,
            $fixers,
            new UnifiedDiffer(),
            $this->getEventDispatcher(),
            $this->getErrorsManager(),
            $linter,
            $dryRun,
            $this->createCacheManager($options, $flags, $dryRun, $rules),
            null,
            false,
        );

        $changes = [];
        $fixResult = $runner->fix();
        foreach ($fixResult as $file => &$data) {
            $file = str_replace(DIRECTORY_SEPARATOR, '/', $file);
            if (strpos($file, $options->getWebRoot()) === 0) {
                $file = substr($file, strlen($options->getWebRoot()));
            }
            $changes[$file] = $data;
        }

        return $changes;
    }

    /**
     * @param string[] $paths
     */
    protected function createFinder(Options $options, array $paths): Finder
    {
        $pathsByType = ['dir' => [], 'file' => []];
        foreach ($paths as $path) {
            if (substr($path, -1) === '/') {
                $pathsByType['dir'][] = $path === '/' ? DIRECTORY_SEPARATOR : str_replace('/', DIRECTORY_SEPARATOR, rtrim($path, '/'));
            } else {
                $pathsByType['file'][] = str_replace('/', DIRECTORY_SEPARATOR, $path);
            }
        }
        $finder = Finder::create()
            ->files()
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude([])
            ->in($pathsByType['dir'])
            ->append($pathsByType['file'])
        ;
        foreach ($options->getFilterByExtensions() as $extension) {
            $finder->name('*.' . $extension);
        }
        foreach ($options->getIgnoredDirectoriesByName() as $notName) {
            $finder->notPath('#(^|/)' . preg_quote($notName, '#') . '(/|$)#');
        }

        return $finder;
    }

    protected function createCacheManager(Options $options, $flags, $dryRun, array $rules): CacheManagerInterface
    {
        if ($options->isCacheDisabled()) {
            return new NullCacheManager();
        }

        return new FileCacheManager(
            new FileHandler($this->getCacheFilename($options, $flags)),
            new Signature(PHP_VERSION, $this->getPhpCSFixerVersion(), '    ', "\n", $rules),
            $dryRun,
            new Directory($options->getWebRoot()),
        );
    }

    protected function getCacheFilename(Options $options, int $flags): string
    {
        return $options->getWebRoot() . '/.php-cs-fixer.cache.' . $flags . '@' . trim(preg_replace('/[^\w\.\@]+/', '_', PHP_VERSION), '_');
    }

    protected function resetErrors(): void
    {
        $this->errorsManager = null;
    }

    protected function getPhpCSFixerVersion(): string
    {
        if ($this->phpCSFixerVersion === null) {
            $phpCSFixerToolInfo = new ToolInfo();
            $this->phpCSFixerVersion = $phpCSFixerToolInfo->getVersion();
        }

        return $this->phpCSFixerVersion;
    }

    private function expandFinder(int $stepIndex): void
    {
        if ($this->steps[$stepIndex]['finder'] instanceof Finder) {
            $this->steps[$stepIndex]['finder'] = new ArrayObject(iterator_to_array($this->steps[$stepIndex]['finder']->getIterator()));
        }
    }
}
