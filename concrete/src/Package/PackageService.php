<?php

namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Database\EntityManagerConfigUpdater;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Localization\Localization;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service class for the package entities and controllers.
 */
class PackageService
{
    /**
     * The Localization service instance.
     *
     * @var Localization
     */
    protected $localization;

    /**
     * The Application container instance.
     *
     * @var Application
     */
    protected $application;

    /**
     * The EntityManagerInterface instance.
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Initialize the instance.
     *
     * @param Localization $localization the Localization service instance
     * @param Application $application the Application container instance
     * @param EntityManagerInterface $entityManager the EntityManagerInterface instance
     */
    public function __construct(Localization $localization, Application $application, EntityManagerInterface $entityManager)
    {
        $this->localization = $localization;
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    /**
     * Get a package entity given its handle.
     *
     * @param string $pkgHandle
     *
     * @return \Concrete\Core\Entity\Package|null
     */
    public function getByHandle($pkgHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');

        return $r->findOneBy(['pkgHandle' => $pkgHandle]);
    }

    /**
     * Get a package entity given its ID.
     *
     * @param int $pkgID
     *
     * @return \Concrete\Core\Entity\Package|null
     */
    public function getByID($pkgID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');

        return $r->findOneBy(['pkgID' => $pkgID]);
    }

    /**
     * Get the package entities of installed packages.
     *
     * @return \Concrete\Core\Entity\Package[]
     */
    public function getInstalledList()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');

        return $r->findBy(['pkgIsInstalled' => true], ['pkgDateInstalled' => 'asc']);
    }

    /**
     * Get the package controllers of the available packages.
     *
     * @param bool $onlyNotInstalled true to get the controllers of not installed packages, false to get all the package controllers
     *
     * @return \Concrete\Core\Package\Package[]
     */
    public function getAvailablePackages($onlyNotInstalled = true)
    {
        $dh = $this->application->make('helper/file');
        $packages = $dh->getDirectoryContents(DIR_PACKAGES);
        if ($onlyNotInstalled) {
            $handles = $this->getInstalledHandles();
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
     * Get the controllers of packages that have newer versions in the local packages directory than those which are in the Packages table.
     * This means they're ready to be upgraded.
     *
     * @return \Concrete\Core\Package\Package[]
     */
    public function getLocalUpgradeablePackages()
    {
        $packages = $this->getAvailablePackages(false);
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
     * Get the handles of all the installed packages.
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
     * Get the controllers of the packages that have an upgraded version available in the marketplace.
     *
     * @return \Concrete\Core\Package\Package[]
     */
    public function getRemotelyUpgradeablePackages()
    {
        $packages = $this->getInstalledList();
        $upgradeables = [];
        foreach ($packages as $p) {
            if (version_compare($p->getPackageVersion(), $p->getPackageVersionUpdateAvailable(), '<')) {
                $upgradeables[] = $p;
            }
        }

        return $upgradeables;
    }

    /**
     * Initialize the entity manager for a package.
     *
     * @param \Concrete\Core\Package\Package $p The controller of package
     * @param bool $clearCache Should the entity metadata cache be emptied?
     */
    public function bootPackageEntityManager(Package $p, $clearCache = false)
    {
        $configUpdater = new EntityManagerConfigUpdater($this->entityManager);
        $providerFactory = new PackageProviderFactory($this->application, $p);
        $provider = $providerFactory->getEntityManagerProvider();
        $configUpdater->addProvider($provider);
        if ($clearCache) {
            $cache = $this->entityManager->getConfiguration()->getMetadataCacheImpl();
            if ($cache) {
                $cache->flushAll();
            }
        }
    }

    /**
     * Uninstall a package.
     *
     * @param \Concrete\Core\Package\Package $p the controller of the package to be uninstalled
     */
    public function uninstall(Package $p)
    {
        $p->uninstall();
        $config = $this->entityManager->getConfiguration();
        $cache = $config->getMetadataCacheImpl();
        if ($cache) {
            $cache->flushAll();
        }
    }

    /**
     * Install a package.
     *
     * @param \Concrete\Core\Package\Package $p the controller of the package to be installed
     * @param array $data data to be passed to the Package::validate_install(), Package::install(), ContentSwapper::swapContent(), ContentSwapper::on_after_swap_content() methods
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList|\Concrete\Core\Package\Package return an ErrorList instance in case of errors, the package controller otherwise
     */
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

        $this->bootPackageEntityManager($p, true);
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
     * Get the controller of a package given its handle.
     *
     * @param string $pkgHandle Handle of package
     *
     * @return Package
     */
    public function getClass($pkgHandle)
    {
        $cache = $this->application->make('cache/request');
        $item = $cache->getItem('package/class/' . $pkgHandle);
        $cl = $item->get();
        if ($item->isMiss()) {
            $item->lock();
            // loads and instantiates the object

            $cl = \Concrete\Core\Foundation\ClassLoader::getInstance();
            $cl->registerPackageController($pkgHandle);

            $class = '\\Concrete\\Package\\' . camelcase($pkgHandle) . '\\Controller';
            try {
                $cl = $this->application->make($class);
            } catch (\Exception $ex) {
                $cl = $this->application->make('Concrete\Core\Package\BrokenPackage', [$pkgHandle]);
            }
            $cache->save($item->set($cl));
        }

        return clone $cl;
    }

    /**
     * @deprecated
     *
     * @param LocalizablePackageInterface $package
     * @param string|null $locale
     * @param \Zend\I18n\Translator\Translator|'current' $translator
     */
    public function setupLocalization(LocalizablePackageInterface $package, $locale = null, $translator = 'current')
    {
        if ($translator === 'current') {
            $translator = $this->localization->getActiveTranslateObject();
        }
        if (is_object($translator)) {
            $locale = (string) $locale;
            if ($locale === '') {
                $locale = $this->localization->getLocale();
            }
            $languageFile = $package->getTranslationFile($locale);
            if (is_file($languageFile)) {
                $translator->addTranslationFile('gettext', $languageFile);
            }
        }
    }
}
