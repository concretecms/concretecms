<?php

declare(strict_types=1);

namespace Concrete\Core\Support\CodingStyle;

use PhpCsFixer\Config;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

defined('C5_EXECUTE') or die('Access Denied.');

final class PHPCSFixerConfigurator
{
    /**
     * @var string
     */
    private const MINIMUM_PHPCSFIXER_VERSION = '3.92.0';

    /**
     * @var \Concrete\Core\Support\CodingStyle\FixerRegistry
     */
    private $fixerRegistry;

    /**
     * @var \Concrete\Core\Support\CodingStyle\RuleCustomisationPolicy
     */
    private $ruleCustomisationPolicy;

    public function __construct(string $minimumPHPVersion)
    {
        self::checkPHPCSFixerVersion();
        $minimumPHPVersion = self::parseMinimumPHPVersionFormat($minimumPHPVersion);
        require_once __DIR__ . '/FileFlag.php';
        require_once __DIR__ . '/FixerRegistry.php';
        require_once __DIR__ . '/RuleCustomisationPolicy.php';
        $this->fixerRegistry = new FixerRegistry();
        $this->ruleCustomisationPolicy = new RuleCustomisationPolicy($this->fixerRegistry, $minimumPHPVersion, $this->detectCoreVersion());
    }

    public function createConfig(bool $parallel = false): Config
    {
        $config = new Config();
        if (class_exists(ParallelConfigFactory::class)) {
            $config->setParallelConfig(
                $parallel
                ? ParallelConfigFactory::detect()
                : ParallelConfigFactory::sequential(),
            );
        }
        $config
            ->registerCustomFixers($this->fixerRegistry->getCustomFixers())
            ->setRiskyAllowed(true)
            ->setRules($this->fixerRegistry->getFixersWithDefaultConfigurations())
            ->setFinder($this->createFinder())
            ->setRuleCustomisationPolicy($this->ruleCustomisationPolicy)
        ;

        return $config;
    }

    public function setEnvironmentVariables(): self
    {
        foreach ([
            'PHP_CS_FIXER_NON_MONOLITHIC' => '1',
        ] as $name => $value) {
            $_ENV[$name] = $value;
            if (function_exists('putenv')) {
                putenv("{$name}={$value}");
            }
        }

        return $this;
    }

    /**
     * @throws \RuntimeException
     */
    public static function checkPHPCSFixerVersion(): void
    {
        $phpCsFixerVersion = defined(Application::class . '::VERSION') ? Application::VERSION : '';
        if ($phpCsFixerVersion === '' || version_compare($phpCsFixerVersion, self::MINIMUM_PHPCSFIXER_VERSION) < 0) {
            $message = 'The minimum PHP CS Fixer version is ' . self::MINIMUM_PHPCSFIXER_VERSION;
            if ($phpCsFixerVersion === '') {
                $message .= ' (you are using an older version)';
            } else {
                $message .= sprintf(' (you are using version %s)', $phpCsFixerVersion);
            }

            throw new \RuntimeException($message);
        }
    }

    /**
     * @throws \RuntimeException
     */
    public static function parseMinimumPHPVersionFormat(string $minimumPHPVersion): string
    {
        $matches = null;
        if (!preg_match('/^\d+(\.\d+){1,2}/', $minimumPHPVersion, $matches)) {
            throw new \RuntimeException("The mimimum PHP version must be in format <mayor>.<minor>[.<patch>]: you provided an invalid value ('{$minimumPHPVersion}')");
        }

        return $matches[0];
    }

    private function createFinder(): Finder
    {
        $finder = new Finder();
        $finder
            ->in(DIR_BASE)
            ->exclude([
                'application/config/doctrine',
                'application/files',
                'tests/assets',
                'concrete/vendor',
            ])
            ->append([
                'concrete/bin/concrete',
                'concrete/bin/concrete5',
                substr(DIR_BASE_CORE, strlen(DIR_BASE) + 1) . '/bin/concrete',
                substr(DIR_BASE_CORE, strlen(DIR_BASE) + 1) . '/bin/concrete5',
            ])
        ;
        if (DIR_BASE . '/concrete' !== DIR_BASE_CORE) {
            $finder
                ->exclude([
                    substr(DIR_BASE_CORE, strlen(DIR_BASE) + 1) . '/vendor',
                ])
                ->append([
                    substr(DIR_BASE_CORE, strlen(DIR_BASE) + 1) . '/bin/concrete',
                    substr(DIR_BASE_CORE, strlen(DIR_BASE) + 1) . '/bin/concrete5',
                ])
            ;
        }

        return $finder;
    }

    private function detectCoreVersion(): string
    {
        if (defined('APP_VERSION')) {
            return \APP_VERSION;
        }
        if (!defined('ASSETS_URL_IMAGES')) {
            define('ASSETS_URL_IMAGES', '');
        }
        $config = require DIR_BASE_CORE . '/config/concrete.php';

        return $config['version'];
    }
}
