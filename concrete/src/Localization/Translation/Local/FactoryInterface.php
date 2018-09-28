<?php
namespace Concrete\Core\Localization\Translation\Local;

use Concrete\Core\Package\Package;

interface FactoryInterface
{
    /**
     * Get stats about all the locally available translation files for the core.
     *
     * @return Stats[] keys are the locale IDs, values are the stats
     */
    public function getAvailableCoreStats();

    /**
     * Get core translations stats for a specific locale ID.
     *
     * @param string $localeID
     *
     * @return Stats
     */
    public function getCoreStats($localeID);

    /**
     * Get stats about all the locally available translation files for a package.
     *
     * @param Package $package
     *
     * @return Stats[] keys are the locale IDs, values are the stats
     */
    public function getAvailablePackageStats(Package $package);

    /**
     * Get package translations stats for a specific locale ID.
     *
     * @param Package $package
     * @param string $localeID
     *
     * @return Stats
     */
    public function getPackageStats(Package $package, $localeID);

    /**
     * Clear (eventually) cached data about local translation stats.
     */
    public function clearCache();
}
