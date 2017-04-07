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

class TranslationsChecker
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
}
