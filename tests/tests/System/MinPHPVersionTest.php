<?php

declare(strict_types=1);

namespace Concrete\Tests\System;

use Concrete\Core\Install\Preconditions\PhpVersion;
use Concrete\Tests\TestCase;

final class MinPHPVersionTest extends TestCase
{
    public function testInstallPreconditions(): void
    {
        $version = self::getMinPHPVersionFromInstallPreconditions();
        self::assertNotNull($version, 'Unable to retrieve the minimum PHP version from the install preconditions');
        self::assertMatchesRegularExpression('/^[1-9]\d*\.(0|[1-9]\d*)(\.(0|[1-9]\d*))?$/D', $version, "'{$version}' is not a valid PHP version");
    }

    public function testComposerPlatform(): void
    {
        if (getenv('CCM_TEST_SKIP_CHECKCOMPOSERPLATFORM')) {
            self::markTestSkipped('Skipping because the CCM_TEST_SKIP_CHECKCOMPOSERPLATFORM environment variable is set');
        }
        $expectedVersion = self::getMinPHPVersionFromInstallPreconditions();
        if ($expectedVersion === null) {
            self::markTestSkipped('Unable to retrieve the minimum PHP version from the install preconditions');
        }
        $composerJsonFile = DIR_BASE . '/composer.json';
        $composerJson = file_get_contents($composerJsonFile);
        self::assertNotFalse($composerJson, "Failed to read the file {$composerJsonFile}");
        $composerConfig = json_decode($composerJson, true);
        self::assertIsArray($composerConfig, "Failed to parse the file {$composerJsonFile}");
        $actualVersion = $composerConfig['config']['platform']['php'] ?? null;
        self::assertIsString($actualVersion, "The file {$composerJsonFile} doesn't define config.platform.php");
        self::assertSame($expectedVersion, $actualVersion, "The value of config.platform.php in the root composer.json file ({$actualVersion}) should be the same as the one defined in the install preconditions ({$expectedVersion})");
    }

    public function testComposerRequirements(): void
    {
        $expectedVersion = self::getMinPHPVersionFromInstallPreconditions();
        if ($expectedVersion === null) {
            self::markTestSkipped('Unable to retrieve the minimum PHP version from the install preconditions');
        }
        $expectedRequirementsRegex = '/^\s*(\^|~|>=)?' . preg_quote($expectedVersion) . '(\s|&|\||$)/';
        $composerJsonFile = DIR_BASE_CORE . '/composer.json';
        $composerJson = file_get_contents($composerJsonFile);
        self::assertNotFalse($composerJson, "Failed to read the file {$composerJsonFile}");
        $composerConfig = json_decode($composerJson, true);
        self::assertIsArray($composerConfig, "Failed to parse the file {$composerJsonFile}");
        $actualRequirements = $composerConfig['require']['php'] ?? null;
        self::assertIsString($actualRequirements, "The file {$composerJsonFile} doesn't have 'php' in the 'require' section");
        self::assertMatchesRegularExpression($expectedRequirementsRegex, $actualRequirements, "The value of require.php in the concrete/composer.json file ({$actualRequirements}) should have {$expectedVersion} as the minimum PHP version");
    }

    public function testDefaultPHPCSFixerPHPVersion(): void
    {
        $expectedVersion = self::getMinPHPVersionFromInstallPreconditions();
        if ($expectedVersion === null) {
            self::markTestSkipped('Unable to retrieve the minimum PHP version from the install preconditions');
        }
        $configFile = DIR_BASE . '/.php-cs-fixer.dist.php';
        $phpCode = file_get_contents($configFile);
        self::assertNotFalse($phpCode, "Failed to read the file {$configFile}");
        $expectedAssignementRegex = '/^\s*\$minimumPHPVersion\s*=\s*["\']' . preg_quote($expectedVersion) . '["\']\s*;/m';
        self::assertMatchesRegularExpression(
            $expectedAssignementRegex,
            $phpCode,
            "The PHP CS Fixer configuration file ({$configFile}) should contain the line \$minimumPHPVersion = '{$expectedVersion}';"
        );
    }

    public function testPHPStanPHPVersion(): void
    {
        $expectedVersion = self::getMinPHPVersionFromInstallPreconditions();
        if ($expectedVersion === null) {
            self::markTestSkipped('Unable to retrieve the minimum PHP version from the install preconditions');
        }
        $versionChunks = array_map('intval', explode('.', $expectedVersion));
        $expectedVersionID = $versionChunks[0] * 10000 + ($versionChunks[1] ?? 0) * 100 + ($versionChunks[2] ?? 0);
        $configFile = DIR_BASE . '/.phpstan/phpstan.neon.dist';
        $neon = file_get_contents($configFile);
        self::assertNotFalse($neon, "Failed to read the file {$configFile}");
        $matches = null;
        self::assertSame(1, preg_match('/^\s*phpVersion\s*:\s*(\d+)\s*$/m', $neon, $matches), "The PHPStan configuration file ({$configFile}) should contain the phpVersion parameter");
        $actualVersionID = (int) $matches[1];
        self::assertSame($expectedVersionID, $actualVersionID, "The value of phpVersion in the PHPStan configuration file ({$actualVersionID}) should correspond to the minimum PHP version defined in the install preconditions ({$expectedVersion}, that is {$expectedVersionID})");
    }

    private static function getMinPHPVersionFromInstallPreconditions(): ?string
    {
        if (!class_exists(PhpVersion::class)) {
            return null;
        }
        if (!defined(PhpVersion::class . '::MINIMUM_PHP_VERSION')) {
            return null;
        }
        return is_string(PhpVersion::MINIMUM_PHP_VERSION) ? PhpVersion::MINIMUM_PHP_VERSION : null;
    }
}
