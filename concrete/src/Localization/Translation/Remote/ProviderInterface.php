<?php
namespace Concrete\Core\Localization\Translation\Remote;

use Gettext\Translations;

interface ProviderInterface
{
    /**
     * List the available translations for a specific core version.
     *
     * @param string $coreVersion The version of the concrete5 core
     * @param int|null $progressLimit A custom progress limit (from 0 - no translations at all - to 100 - all strings are translated)
     *
     * @return Stats[] Array keys are the locale IDs, values are Stats instances
     */
    public function getAvailableCoreStats($coreVersion, $progressLimit = null);

    /**
     * Get core translations stats for a specific locale ID.
     *
     * @param string $coreVersion The version of the concrete5 core
     * @param string $localeID
     * @param int|null $progressLimit A custom progress limit (from 0 - no translations at all - to 100 - all strings are translated)
     *
     * @return Stats
     */
    public function getCoreStats($coreVersion, $localeID, $progressLimit = null);

    /**
     * List the available translations for a specific package version.
     *
     * @param string $packageHandle The handle of the package
     * @param string $packageVersion The version of the package
     * @param int|null $progressLimit A custom progress limit (from 0 - no translations at all - to 100 - all strings are translated)
     *
     * @return Stats[] Array keys are the locale IDs, values are Stats instances
     */
    public function getAvailablePackageStats($packageHandle, $packageVersion, $progressLimit = null);

    /**
     * Get package translations stats for a specific locale ID.
     *
     * @param string $packageHandle The handle of the package
     * @param string $packageVersion The version of the package
     *
     * @return Stats
     */
    public function getPackageStats($packageHandle, $packageVersion, $localeID, $progressLimit = null);

    /**
     * Fetch the translations for the concrete5 core.
     *
     * @param string $coreVersion The version of the concrete5 core
     * @param string $localeID The locale identifier
     * @param string $formatHandle The handle of the format of the translations to be fetched
     *
     * @return string
     */
    public function fetchCoreTranslations($coreVersion, $localeID, $formatHandle = 'mo');

    /**
     * Fetch the translations for a package.
     *
     * @param string $packageHandle The handle of the package
     * @param string $packageVersion The version of the package
     * @param string $localeID The locale identifier
     * @param string $formatHandle The handle of the format of the translations to be fetched
     *
     * @return string
     */
    public function fetchPackageTranslations($packageHandle, $packageVersion, $localeID, $formatHandle = 'mo');

    /**
     * Fill-in already known translations.
     *
     * @param Translations $translations
     *
     * @return Translations
     */
    public function fillTranslations(Translations $translations);
}
