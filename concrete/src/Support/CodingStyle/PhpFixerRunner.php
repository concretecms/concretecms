<?php

namespace Concrete\Core\Support\CodingStyle;

use ArrayObject;
use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\FileCacheManager;
use PhpCsFixer\Cache\FileHandler;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Cache\Signature;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\ToolInfo;
use Concrete\Core\Events\EventDispatcher;
use Symfony\Component\Finder\Finder;
use Traversable;

/**
 * Wrapper to PHP-CS-Fixer.
 */
class PhpFixerRunner
{
    /**
     * @var \Concrete\Core\Support\CodingStyle\PhpFixerRuleResolver
     */
    protected $ruleResolver;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $steps;

    /**
     * @var \PhpCsFixer\Error\ErrorsManager
     */
    protected $errorManager;

    /**
     * @var string|null
     */
    private $phpCSFixerVersion;

    /**
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerRuleResolver $ruleResolver
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(PhpFixerRuleResolver $ruleResolver, EventDispatcher $eventDispatcher)
    {
        $this->ruleResolver = $ruleResolver;
        $this->eventDispatcher = $eventDispatcher;
        $this->resetSteps();
        $this->resetErrors();
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerOptions $options
     * @param array $paths
     * @param int $flags
     *
     * @return $this
     */
    public function addStep(PhpFixerOptions $options, array $paths, $flags)
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
    public function resetSteps()
    {
        $this->steps = [];

        return $this;
    }

    /**
     * @return \PhpCsFixer\Error\ErrorsManager
     */
    public function getErrorManager()
    {
        return $this->errorManager;
    }

    /**
     * @return int
     */
    public function calculateNumberOfFiles()
    {
        $sum = 0;
        foreach (array_keys($this->steps) as $stepIndex) {
            $this->expandFinder($stepIndex);
            $sum += count($this->steps[$stepIndex]['finder']);
        }

        return $sum;
    }

    /**
     * @param bool $dryRun
     *
     * @return array
     */
    public function apply($dryRun)
    {
        $this->resetErrors();
        $changes = [];
        foreach ($this->steps as $step) {
            $changes += $this->applyStep($step['options'], $step['finder'], $step['flags'], $dryRun);
        }

        return $changes;
    }

    /**
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerOptions $options
     * @param \Symfony\Component\Finder\Finder|\ArrayObject $finder
     * @param int $flags
     * @param bool $dryRun
     *
     * @return array|void[]|NULL[]|string[][]|string[][][]
     */
    protected function applyStep(PhpFixerOptions $options, Traversable $finder, $flags, $dryRun)
    {
        $rules = $this->ruleResolver->getRules($flags, true, $options->getMinimumPhpVersion());
        $fixers = $this->ruleResolver->getFixers($rules);

        $linter = new Linter();
        $runner = new Runner(
            $finder,
            $fixers,
            new Differ(),
            $this->eventDispatcher->getEventDispatcher(),
            $this->errorManager,
            $linter,
            $dryRun,
            $this->createCacheManager($options, $flags, $dryRun, $rules),
            null,
            false
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
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerOptions $options
     * @param array $paths
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function createFinder(PhpFixerOptions $options, array $paths)
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

    /**
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerOptions $options
     * @param int $flags
     * @param bool $dryRun
     * @param array $rules
     *
     * @return \PhpCsFixer\Cache\CacheManagerInterface
     */
    protected function createCacheManager(PhpFixerOptions $options, $flags, $dryRun, array $rules)
    {
        if ($options->isCacheDisabled()) {
            return new NullCacheManager();
        }

        return new FileCacheManager(
            new FileHandler($this->getCacheFilename($options, $flags)),
            new Signature(
                PHP_VERSION,
                $this->getPhpCSFixerVersion(),
                '    ',
                "\n",
                $rules
            ),
            $dryRun,
            new Directory($options->getWebRoot())
        );
    }

    /**
     * @param \Concrete\Core\Support\CodingStyle\PhpFixerOptions $options
     * @param int $flags
     *
     * @return string
     */
    protected function getCacheFilename(PhpFixerOptions $options, $flags)
    {
        return $options->getWebRoot() . '/.php_cs.cache.' . $flags . '@' . trim(preg_replace('/[^\w\.\@]+/', '_', PHP_VERSION), '_');
    }

    protected function resetErrors()
    {
        $this->errorManager = new ErrorsManager();
    }

    /**
     * @return string
     */
    protected function getPhpCSFixerVersion()
    {
        if ($this->phpCSFixerVersion === null) {
            $phpCSFixerToolInfo = new ToolInfo();
            $this->phpCSFixerVersion = $phpCSFixerToolInfo->getVersion();
        }

        return $this->phpCSFixerVersion;
    }

    /**
     * @param int $stepIndex
     */
    private function expandFinder($stepIndex)
    {
        if ($this->steps[$stepIndex]['finder'] instanceof Finder) {
            $this->steps[$stepIndex]['finder'] = new ArrayObject(iterator_to_array($this->steps[$stepIndex]['finder']->getIterator()));
        }
    }
}
