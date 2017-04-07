<?php
namespace Concrete\Core\Localization\Service;

use Concrete\Core\Application\Application;
use Concrete\Core\Localization\Translation\Local\FactoryInterface as LocalFactory;
use Concrete\Core\Localization\Translation\Local\Stats as LocalStats;
use Concrete\Core\Localization\Translation\LocaleStatus;
use Concrete\Core\Localization\Translation\LocalRemoteCouple;
use Concrete\Core\Localization\Translation\PackageLocaleStatus;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteProvider;
use Concrete\Core\Localization\Translation\Remote\Stats as RemoteStats;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Exception;
use Illuminate\Filesystem\Filesystem;

class TranslationsUpdater
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var LocalFactory
     */
    protected $localFactory;

    /**
     * @var RemoteProvider
     */
    protected $remoteProvider;

    /**
     * @var Filesystem|null
     */
    protected $fs = null;

    /**
     * @param LocalTranslationFactoryInterface $localFactory
     * @param RemoteTranslationsProviderInterface $remoteProvider
     */
    public function __construct(Application $app, LocalFactory $localFactory, RemoteProvider $remoteProvider)
    {
        $this->app = $app;
        $this->localFactory = $localFactory;
        $this->remoteProvider = $remoteProvider;
    }

    /**
     * @param Filesystem $fs
     */
    public function setFilesystem(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        if ($this->fs === null) {
            $this->fs = new Filesystem();
        }

        return $this->fs;
    }

    /**
     * @return LocaleStatus
     */
    public function getCoreTranslations()
    {
        $coreVersion = $this->app->make('config')->get('concrete.version_installed');
        $localStats = $this->localFactory->getAvailableCoreStats();
        $remoteStats = $this->remoteProvider->getAvailableCoreStats($coreVersion);

        return $this->computeUpdates($localStats, $remoteStats);
    }

    /**
     * @param bool $onlyInstalled
     *
     * @return PackageLocaleStatus[]
     */
    public function getPackagesTranslations($onlyInstalled = false)
    {
        $result = [];
        $ps = $this->app->make(PackageService::class);
        foreach ($ps->getAvailablePackages($onlyInstalled) as $package) {
            $result[] = $this->getPackageTranslations($package);
        }

        return $result;
    }

    /**
     * @param Package $package
     *
     * @return PackageLocaleStatus
     */
    public function getPackageTranslations(Package $package)
    {
        $localStats = $this->localFactory->getAvailablePackageStats($package);
        $remoteStats = $this->remoteProvider->getAvailablePackageStats($package->getPackageHandle(), $package->getPackageVersion());

        return $this->computeUpdates($localStats, $remoteStats, $package);
    }

    /**
     * @param LocalStats[] $localStats
     * @param RemoteStats[] $remoteStats
     *
     * @return PackageLocaleStatus|LocaleStatus
     */
    private function computeUpdates(array $localStats, array $remoteStats, Package $package = null)
    {
        if ($package === null) {
            $result = new LocaleStatus();
        } else {
            $result = new PackageLocaleStatus($package);
        }
        foreach ($remoteStats as $remoteLocaleID => $remoteInfo) {
            if ($remoteInfo->getTranslated() === 0) {
                continue;
            }
            $localInfo = isset($localStats[$remoteLocaleID]) ? $localStats[$remoteLocaleID] : null;
            if ($localInfo === null) {
                $result->addOnlyRemote($remoteLocaleID, $remoteInfo);
            } else {
                $couple = new LocalRemoteCouple($localInfo, $remoteInfo);
                if ($localInfo->getUpdatedOn() === null || $localInfo->getUpdatedOn() < $remoteInfo->getUpdatedOn() || $localInfo->getVersion() !== $remoteInfo->getVersion()) {
                    $result->addInstalledOutdated($remoteLocaleID, $couple);
                } else {
                    $result->addInstalledUpdated($remoteLocaleID, $couple);
                }
            }
        }
        foreach ($localStats as $localLocaleID => $localInfo) {
            if (!isset($remoteStats[$localLocaleID])) {
                $result->addOnlyLocal($localLocaleID, $localInfo);
            }
        }

        return $result;
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
     * @param string $localeID
     * @param Package|null $package
     *
     * @throws Exception
     */
    private function installTranslations($localeID, Package $package = null)
    {
        if ($package === null) {
            $shownDirectoryName = '/' . DIRNAME_APPLICATION . '/' . DIRNAME_LANGUAGES;
        } else {
            $shownDirectoryName = '/' . DIRNAME_PACKAGES . '/' . $package->getPackageHandle() . '/' . DIRNAME_LANGUAGES;
        }
        if ($package === null) {
            $localStats = $this->localFactory->getCoreStats($localeID);
        } else {
            $localStats = $this->localFactory->getPackageStats($package, $localeID);
        }
        $directory = dirname($localStats->getFilename());
        $fs = $this->getFilesystem();
        if (!$fs->isDirectory($directory)) {
            if ($fs->makeDirectory($directory, DIRECTORY_PERMISSIONS_MODE_COMPUTED, true, true) !== true) {
                throw new Exception(t('Failed to create the directory for the language file. Please be sure that the %s directory is writable', $shownDirectoryName));
            }
        }
        if (!$fs->isWritable($directory)) {
            throw new Exception(t('Please be sure that the %s directory is writable', $shownDirectoryName));
        }
        if ($package === null) {
            $coreVersion = $this->app->make('config')->get('concrete.version_installed');
            $translations = $this->remoteProvider->fetchCoreTranslations($coreVersion, $localeID);
        } else {
            $translations = $this->remoteProvider->fetchPackageTranslations($package->getPackageHandle(), $package->getPackageVersion(), $localeID);
        }
        $saved = @$fs->put($localStats->getFilename(), $translations, true) !== false;
        unset($translations);
        if ($saved === false) {
            throw new Exception(t('Failed to save the language file: please be sure that PHP can write files in the directory %s', $shownDirectoryName));
        }
    }
}
