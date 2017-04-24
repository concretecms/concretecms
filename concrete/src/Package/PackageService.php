<?php
namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Database\EntityManagerConfigUpdater;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Localization\Localization;
use Doctrine\ORM\EntityManagerInterface;

class PackageService
{
    protected $entityManager;
    protected $application;
    protected $localization;

    public function __construct(
        Localization $localization,
        Application $application,
        EntityManagerInterface $entityManager
    ) {
        $this->application = $application;
        $this->localization = $localization;
        $this->entityManager = $entityManager;
    }

    public function getByHandle($pkgHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');

        return $r->findOneBy(['pkgHandle' => $pkgHandle]);
    }

    public function getByID($pkgID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');

        return $r->findOneBy(['pkgID' => $pkgID]);
    }

    /**
     * Returns an array of all installed packages.
     *
     * @return \Concrete\Core\Entity\Package[]
     */
    public function getInstalledList()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');

        return $r->findBy(['pkgIsInstalled' => true], ['pkgDateInstalled' => 'asc']);
    }

    /**
     * Returns all available packages.
     *
     * @param bool $filterInstalled True to only return not installed packages
     *
     * @return Package[]
     */
    public function getAvailablePackages($filterInstalled = true)
    {
        $dh = $this->application->make('helper/file');
        $packages = $dh->getDirectoryContents(DIR_PACKAGES);
        if ($filterInstalled) {
            $handles = self::getInstalledHandles();
            $packages = array_diff($packages, $handles);
        }

        if (count($packages) > 0) {
            $packagesTemp = [];
            // get package objects from the file system
            foreach ($packages as $p) {
                if (file_exists(DIR_PACKAGES . '/' . $p . '/' . FILENAME_CONTROLLER)) {
                    $pkg = $this->getClass($p);
                    if (!empty($pkg)) {
                        $packagesTemp[] = $pkg;
                    }
                }
            }
            $packages = $packagesTemp;
        }

        return $packages;
    }

    /**
     * Returns an array of packages that have newer versions in the local packages directory
     * than those which are in the Packages table. This means they're ready to be upgraded.
     *
     * @return Package[]
     */
    public function getLocalUpgradeablePackages()
    {
        $packages = self::getAvailablePackages(false);
        $upgradeables = [];
        foreach ($packages as $p) {
            $entity = $this->getByHandle($p->getPackageHandle());
            if ($entity) {
                if (version_compare($p->getPackageVersion(), $entity->getPackageVersion(), '>')) {
                    $p->pkgCurrentVersion = $entity->getPackageVersion();
                    $upgradeables[] = $p;
                }
            }
        }

        return $upgradeables;
    }

    /**
     * Returns all installed package handles.
     *
     * @return string[]
     */
    public function getInstalledHandles()
    {
        $query = 'select p.pkgHandle from \\Concrete\\Core\\Entity\\Package p';
        $r = $this->entityManager->createQuery($query);
        $result = $r->getArrayResult();
        $handles = [];
        foreach ($result as $r) {
            $handles[] = $r['pkgHandle'];
        }

        return $handles;
    }

    /**
     * Finds all packages that have an upgraded version available in the marketplace.
     *
     * @return Package[]
     */
    public function getRemotelyUpgradeablePackages()
    {
        $packages = self::getInstalledList();
        $upgradeables = [];
        foreach ($packages as $p) {
            if (version_compare($p->getPackageVersion(), $p->getPackageVersionUpdateAvailable(), '<')) {
                $upgradeables[] = $p;
            }
        }

        return $upgradeables;
    }

    public function setupLocalization(LocalizablePackageInterface $package, $locale = null, $translate = 'current')
    {
        if ($translate === 'current') {
            $translate = \Localization::getTranslate();
        }
        if (is_object($translate)) {
            if (!isset($locale) || !strlen($locale)) {
                $locale = Localization::activeLocale();
            }
            $languageFile = $package->getTranslationFile($locale);
            if (is_file($languageFile)) {
                $translate->addTranslationFile('gettext', $languageFile);
            }
        }
    }

    public function bootPackageEntityManager(Package $p)
    {
        $configUpdater = new EntityManagerConfigUpdater($this->entityManager);
        $providerFactory = new PackageProviderFactory($this->application, $p);
        $provider = $providerFactory->getEntityManagerProvider();
        $configUpdater->addProvider($provider);
    }

    public function uninstall(Package $p)
    {
        $p->uninstall();
        $config = $this->entityManager->getConfiguration();
        $cache = $config->getMetadataCacheImpl();
        $cache->flushAll();
    }

    public function install(Package $p, $data)
    {
        $this->localization->pushActiveContext(Localization::CONTEXT_SYSTEM);
        ClassLoader::getInstance()->registerPackage($p);

        if (method_exists($p, 'validate_install')) {
            $response = $p->validate_install($data);
        }

        if (isset($response) && $response instanceof ErrorList && $response->has()) {
            return $response;
        }

        $this->bootPackageEntityManager($p);
        $p->install($data);

        $u = new \User();
        $swapper = $p->getContentSwapper();
        if ($u->isSuperUser() && $swapper->allowsFullContentSwap($p) && $data['pkgDoFullContentSwap']) {
            $swapper->swapContent($p, $data);
            if (method_exists($p, 'on_after_swap_content')) {
                $p->on_after_swap_content($data);
            }
        }
        $this->localization->popActiveContext();
        $pkg = $this->getByHandle($p->getPackageHandle());

        return $p;
    }

    /**
     * Returns a package's class.
     *
     * @param string $pkgHandle Handle of package
     *
     * @return Package
     */
    public function getClass($pkgHandle)
    {
        $app = \Core::make('app');
        $cache = $app->make('cache/request');
        $item = $cache->getItem('package/class/' . $pkgHandle);
        $cl = $item->get();
        if ($item->isMiss()) {
            $item->lock();
            // loads and instantiates the object

            $cl = \Concrete\Core\Foundation\ClassLoader::getInstance();
            $cl->registerPackageController($pkgHandle);

            $class = '\\Concrete\\Package\\' . camelcase($pkgHandle) . '\\Controller';
            try {
                $cl = $app->make($class);
            } catch (\Exception $ex) {
                $cl = $app->make('Concrete\Core\Package\BrokenPackage', [$pkgHandle]);
            }
            $cache->save($item->set($cl));
        }

        return clone $cl;
    }
}
