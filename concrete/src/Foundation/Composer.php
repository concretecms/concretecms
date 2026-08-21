<?php

declare(strict_types=1);

namespace Concrete\Core\Foundation;

use Composer\InstalledVersions;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * A service class that inspects the composer (https://getcomposer.org/) environment.
 */
final class Composer
{
    /**
     * @var bool|null
     */
    private $coreInstalledViaComposer = null;

    /**
     * @var bool
     */
    private $coreInstalledViaComposerDetected = false;

    /**
     * @var string[]|null
     */
    private $packagesInstalledViaComposer = null;

    /**
     * Check if ConcreteCMS core is installed via composer.
     * Returns NULL if we are unsure
     */
    public function isCoreInstalledViaComposer(): ?bool
    {
        if ($this->coreInstalledViaComposerDetected === false) {
            $this->coreInstalledViaComposer = $this->detectCoreInstalledViaComposer();
            $this->coreInstalledViaComposerDetected = true;
        }

        return $this->coreInstalledViaComposer;
    }

    /**
     * Check if a ConcreteCMS package is installed via composer.
     */
    public function isPackageInstalledViaComposer(string $packageHandle): bool
    {
        return in_array($packageHandle, $this->getPackagesInstalledViaComposer(), true);
    }

    /**
     * Get the handles of all the packages installed via composer.
     *
     * @return string[]
     */
    public function getPackagesInstalledViaComposer(): array
    {
        if ($this->packagesInstalledViaComposer === null) {
            $this->packagesInstalledViaComposer = $this->detectPackagesInstalledViaComposer();
        }

        return $this->packagesInstalledViaComposer;
    }

    private function detectCoreInstalledViaComposer(): ?bool
    {
        if (\APP_UPDATED_PASSTHRU === true) {
            // If ConcreteCMS has been upgraded by placing the core in the updates directory, it means it's a ZIP distribution
            return false;
        }
        $root = InstalledVersions::getRootPackage();
        if (($root['name'] ?? null) !== 'concrete5/concrete5') {
            // In ConcreteCMS zip distributions, the "name" in the root composer.json is 'concrete5/concrete5'
            return true;
        }
        if (!InstalledVersions::isInstalled('wikimedia/composer-merge-plugin', false)) {
            // In ConcreteCMS zip distributions, the core is loaded by wikimedia/composer-merge-plugin
            return true;
        }
        $rootComposerJson = \DIR_BASE . '/composer.json';
        if (is_file($rootComposerJson)) {
            // In ConcreteCMS zip distributions, composer.json is in the webroot (if not deleted by users)
            $rootComposer = json_decode(file_get_contents($rootComposerJson), true);

            // In ConcreteCMS zip distributions, the core is loaded by wikimedia/composer-merge-plugin with the extra.merge-plugin.include entry in config.json
            return in_array('concrete/composer.json', $rootComposer['extra']['merge-plugin']['include'] ?? [], true) ? false : true;
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function detectPackagesInstalledViaComposer(): array
    {
        // See https://github.com/composer/installers/blob/main/README.md#current-supported-package-types
        $types = ['concretecms-package', 'concrete5-package'];
        $result = [];
        foreach ($types as $type) {
            foreach (InstalledVersions::getInstalledPackagesByType($type) as $name) {
                [, $packageHandle] = explode('/', $name, 2);
                $result[] = $packageHandle;
            }
        }

        return array_values(array_unique($result));
    }
}
