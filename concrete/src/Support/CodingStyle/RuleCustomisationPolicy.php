<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle;

use PhpCsFixer\Config\RuleCustomisationPolicyInterface;

defined('C5_EXECUTE') or die('Access Denied.');

final class RuleCustomisationPolicy implements RuleCustomisationPolicyInterface
{
    /**
     * @var \Concrete\Core\Support\CodingStyle\FixerRegistry
     */
    private $fixerRegistry;

    /**
     * @var string
     */
    private $minimumPHPVersion;

    /**
     * @var string
     */
    private $coreVersion;

    /**
     * @var array{\SplFileInfo, int}|null
     */
    private $lastResolvedFlags = null;

    /**
     * @var array<string, \PhpCsFixer\Fixer\FixerInterface|bool>
     */
    private $filterByFlagsCache = [];

    /**
     * @var array<string, \Closure(\SplFileInfo): (PhpCsFixer\Fixer\FixerInterface|bool)
     */
    private $ruleCustomizers;

    public function __construct(FixerRegistry $fixerRegistry, string $minimumPHPVersion, string $coreVersion)
    {
        $this->fixerRegistry = $fixerRegistry;
        $this->minimumPHPVersion = $minimumPHPVersion;
        $this->coreVersion = $coreVersion;
        $this->ruleCustomizers = [];
        foreach ($this->fixerRegistry->getFixerNamesWithCustomizationRules() as $fixerName) {
            $this->ruleCustomizers[$fixerName] = function (\SplFileInfo $file) use ($fixerName) {
                return $this->customizeRule($fixerName, $file);
            };
        }
    }

    public function getMinimumPHPVersion(): string
    {
        return $this->minimumPHPVersion;
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Config\RuleCustomisationPolicyInterface::getPolicyVersionForCache()
     */
    public function getPolicyVersionForCache(): string
    {
        return "concrete-{$this->coreVersion}";
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Config\RuleCustomisationPolicyInterface::getRuleCustomisers()
     */
    public function getRuleCustomisers(): array
    {
        return $this->ruleCustomizers;
    }

    /**
     * @return \PhpCsFixer\Fixer\FixerInterface|bool
     */
    private function customizeRule(string $fixerName, \SplFileInfo $file)
    {
        $fileFlags = $this->getFileFlags($file);
        $cacheKey = "{$fixerName}@{$fileFlags}";
        if (!isset($this->filterByFlagsCache[$cacheKey])) {
            $this->filterByFlagsCache[$cacheKey] = $this->fixerRegistry->reconfigure($fixerName, $fileFlags, $this->minimumPHPVersion);
        }

        return $this->filterByFlagsCache[$cacheKey];
    }

    private function getFileFlags(\SplFileInfo $file): int
    {
        if ($this->lastResolvedFlags === null || $this->lastResolvedFlags[0] !== $file) {
            $this->lastResolvedFlags = [$file, FileFlag::resolveFileFlags($file)];
        }

        return $this->lastResolvedFlags[1];
    }
}
