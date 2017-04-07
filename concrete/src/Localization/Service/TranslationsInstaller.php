<?php
namespace Concrete\Core\Localization\Service;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Localization\Translation\Local\FactoryInterface as LocalFactory;
use Concrete\Core\Localization\Translation\Remote\ProviderInterface as RemoteProvider;
use Concrete\Core\Package\Package;
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
     * @param Repository $config
     * @param LocalFactory $localFactory
     * @param RemoteProvider $remoteProvider
     * @param Filesystem $fs
     */
    public function __construct(Repository $config, LocalFactory $localFactory, RemoteProvider $remoteProvider, Filesystem $fs)
    {
        $this->config = $config;
        $this->localFactory = $localFactory;
        $this->remoteProvider = $remoteProvider;
        $this->fs = $fs;
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
