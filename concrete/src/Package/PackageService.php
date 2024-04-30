<?php

namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManagerConfigUpdater;
use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\ClassAutoloader;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Marketplace\PackageRepository;
use Concrete\Core\Marketplace\Update\Command\UpdateRemoteDataCommand;
use Concrete\Core\Marketplace\Update\Inspector;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

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

    public function checkPackageUpdates(PackageRepository $repository, array $skipHandles = []): void
    {
        $connection = $repository->getConnection();
        if (!$connection) {
            return;
        }

        $versions = [];
        $remotePackages = $repository->getPackages($connection, true);
        foreach ($remotePackages as $remotePackage) {
            $versions[$remotePackage->handle] = $remotePackage->version;
        }
        $remotePackage = null;

        $count = 0;
        foreach ($this->getInstalledList() as $package) {
            if (in_array($package->getPackageHandle(), $skipHandles, true)) {
                continue;
            }

            $package->setPackageAvailableVersion($versions[$package->getPackageHandle()] ?? null);
            if (++$count === 10) {
                $count = 0;
                $this->entityManager->flush();
            }
        }

        if ($count > 0) {
            $this->entityManager->flush();
        }
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
        $inspector = $this->application->make(Inspector::class);
        $command = new UpdateRemoteDataCommand([$inspector->getPackagesField()]);
        $this->application->executeCommand($command);
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

        if (method_exists($p, 'validate_install')) {
            $response = $p->validate_install($data);
        }

        if (isset($response) && $response instanceof ErrorList && $response->has()) {
            return $response;
        }

        $this->bootPackageEntityManager($p, true);
        $p->install($data);

        $inspector = $this->application->make(Inspector::class);
        $command = new UpdateRemoteDataCommand([$inspector->getPackagesField()]);
        $this->application->executeCommand($command);

        $u = $this->application->make(User::class);
        $swapper = $p->getContentSwapper();
        if ($u->isSuperUser() && $swapper->allowsFullContentSwap($p) && isset($data['pkgDoFullContentSwap']) && $data['pkgDoFullContentSwap']) {
            $swapper->swapContent($p, $data);
            if (method_exists($p, 'on_after_swap_content')) {
                $p->on_after_swap_content($data);
            }
        }
        $this->localization->popActiveContext();
        $this->getByHandle($p->getPackageHandle());

        return $p;
    }

    /**
     * Get the controller of a package given its handle.
     *
     * @param string $pkgHandle Handle of package
     *
     * @return \Concrete\Core\Package\Package
     */
    public function getClass($pkgHandle)
    {
        $cache = $this->application->make('cache/request');
        $item = $cache->getItem('package/class/' . $pkgHandle);
        if ($item->isMiss()) {
            $item->lock();
            $classAutoloader = ClassAutoloader::getInstance();
            $classAutoloader->registerPackageHandle($pkgHandle);
            // loads and instantiates the object
            $class = '\\Concrete\\Package\\' . camelcase($pkgHandle) . '\\Controller';
            $packageController = null;
            try {
                $packageController = $this->application->make($class);
                if (!$packageController instanceof Package) {
                    $packageController = null;
                    $errorDetails = t('The package controller does not extend the PHP class %s', Package::class);
                }
            } catch (Throwable $x) {
                $errorDetails = $x->getMessage();
            }
            if ($packageController === null) {
                $classAutoloader->unregisterPackage($pkgHandle);
                $packageController = $this->application->make(BrokenPackage::class, ['pkgHandle' => $pkgHandle, 'errorDetails' => $errorDetails]);
            } else {
                $classAutoloader->registerPackageController($packageController);
            }
            $cache->save($item->set($packageController));
        } else {
            $packageController = $item->get();
        }

        return clone $packageController;
    }

    /**
     * @deprecated
     *
     * @param LocalizablePackageInterface $package
     * @param string|null $locale
     * @param \Laminas\I18n\Translator\Translator|'current' $translator
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
