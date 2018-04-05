<?php

namespace Concrete\Core\Localization\Service;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Localization\Translation\Local\FactoryInterface as LocalFactory;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteProvider;
use Concrete\Core\Package\Package;
use Concrete\Core\Site\Service as SiteService;
use Exception;
use Illuminate\Filesystem\Filesystem;

class TranslationsInstaller
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var LocalFactory
     */
    protected $localFactory;

    /**
     * @var RemoteProvider
     */
    protected $remoteProvider;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Repository $config
     * @param LocalFactory $localFactory
     * @param RemoteProvider $remoteProvider
     * @param Filesystem $fs
     * @param Application $app
     */
    public function __construct(Repository $config, LocalFactory $localFactory, RemoteProvider $remoteProvider, Filesystem $fs, Application $app)
    {
        $this->config = $config;
        $this->localFactory = $localFactory;
        $this->remoteProvider = $remoteProvider;
        $this->fs = $fs;
        $this->app = $app;
    }

    /**
     * Install a new core locale, or update an existing one.
     *
     * @param string $localeID
     *
     * @throws Exception
     */
    public function installCoreTranslations($localeID)
    {
        $this->installTranslations($localeID, null);
    }

    /**
     * Install a new locale for a package, or update an existing one.
     *
     * @param Package $package
     * @param string $localeID
     *
     * @throws Exception
     */
    public function installPackageTranslations(Package $package, $localeID)
    {
        $this->installTranslations($localeID, $package);
    }

    /**
     * Install missing package translation files.
     *
     * @param Package $package
     *
     * @return array array keys are the missing locale IDs, array values are: false (package not translated), true (language file downloaded), \Exception (in case of errors)
     */
    public function installMissingPackageTranslations(Package $package)
    {
        // Get the list of languages that users may need for the user interface.
        $wanted = Localization::getAvailableInterfaceLanguages();
        // Get the list of languages that users may need for the site locales.
        $siteService = $this->app->make(SiteService::class);
        $site = $siteService->getSite();
        if ($site) {
            foreach ($site->getLocales() as $locale) {
                $wanted[] = $locale->getLocale();
            }
        }
        $wanted = array_unique($wanted);
        $wanted = array_filter($wanted, function ($localeID) {
            return $localeID !== Localization::BASE_LOCALE;
        });
        $already = array_keys($this->localFactory->getAvailablePackageStats($package));
        $missing = array_diff($wanted, $already);
        $result = [];
        if (count($missing) > 0) {
            $available = $this->remoteProvider->getAvailablePackageStats($package->getPackageHandle(), $package->getPackageVersion());
            $toDownload = array_intersect($missing, array_keys($available));
            foreach ($missing as $missingID) {
                if (!in_array($missingID, $toDownload, true)) {
                    $result[$missingID] = false;
                } else {
                    try {
                        $this->installPackageTranslations($package, $missingID);
                        $result[$missingID] = true;
                    } catch (Exception $x) {
                        $result[$missingID] = $x;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $localeID
     * @param Package|null $package
     *
     * @throws Exception
     */
    private function installTranslations($localeID, Package $package = null)
    {
        if ($package === null) {
            $localStats = $this->localFactory->getCoreStats($localeID);
            $shownDirectoryName = '/' . DIRNAME_APPLICATION . '/' . DIRNAME_LANGUAGES;
        } else {
            $localStats = $this->localFactory->getPackageStats($package, $localeID);
            $shownDirectoryName = '/' . DIRNAME_PACKAGES . '/' . $package->getPackageHandle() . '/' . DIRNAME_LANGUAGES;
        }
        $directory = dirname($localStats->getFilename());
        if (!$this->fs->isDirectory($directory)) {
            if ($this->fs->makeDirectory($directory, DIRECTORY_PERMISSIONS_MODE_COMPUTED, true, true) !== true) {
                throw new Exception(t('Failed to create the directory for the language file. Please be sure that the %s directory is writable', $shownDirectoryName));
            }
        }
        if (!$this->fs->isWritable($directory)) {
            throw new Exception(t('Please be sure that the %s directory is writable', $shownDirectoryName));
        }
        if ($package === null) {
            $coreVersion = $this->config->get('concrete.version_installed');
            $translations = $this->remoteProvider->fetchCoreTranslations($coreVersion, $localeID);
        } else {
            $translations = $this->remoteProvider->fetchPackageTranslations($package->getPackageHandle(), $package->getPackageVersion(), $localeID);
        }
        $saved = @$this->fs->put($localStats->getFilename(), $translations, true) !== false;
        unset($translations);
        if ($saved === false) {
            throw new Exception(t('Failed to save the language file: please be sure that PHP can write files in the directory %s', $shownDirectoryName));
        }
    }
}
