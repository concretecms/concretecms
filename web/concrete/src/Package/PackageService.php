<?php
namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\Item\Manager\ItemInterface;
use Concrete\Core\Package\Item\Manager\Manager;
use Concrete\Core\Page\Theme\Theme;
use Doctrine\ORM\EntityManagerInterface;

class PackageService
{

    protected $entityManager;
    protected $application;

    public function __construct(Application $application, EntityManagerInterface $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    public function getByHandle($pkgHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');
        return $r->findOneBy(array('pkgHandle' => $pkgHandle));
    }

    public function getByID($pkgID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');
        return $r->findOneBy(array('pkgID' => $pkgID));
    }


    /**
     * Returns an array of all installed packages.
     *
     * @return \Concrete\Core\Entity\Package[]
     */
    public function getInstalledList()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Package');
        return $r->findAll(array('pkgIsInstalled' => true), array('pkgDateInstalled', 'asc'));
    }

    /**
     * Returns all available packages.
     *
     * @param bool $filterInstalled True to only return installed packages
     *
     * @return Package[]
     */
    public function getAvailablePackages($filterInstalled = true)
    {
        $dh = $this->application->make('helper/file');
        $packages = $dh->getDirectoryContents(DIR_PACKAGES);
        if ($filterInstalled) {
            $handles = self::getInstalledHandles();

            // strip out packages we've already installed
            $packagesTemp = array();
            foreach ($packages as $p) {
                if (!in_array($p, $handles)) {
                    $packagesTemp[] = $p;
                }
            }
            $packages = $packagesTemp;
        }

        if (count($packages) > 0) {
            $packagesTemp = array();
            // get package objects from the file system
            foreach ($packages as $p) {
                if (file_exists(DIR_PACKAGES . '/' . $p . '/' . FILENAME_CONTROLLER)) {
                    $pkg = static::getClass($p);
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
        $upgradeables = array();
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
        $query = "select p.pkgHandle from \\Concrete\\Core\\Entity\\Package p";
        $r = $this->entityManager->createQuery($query);
        $result = $r->getArrayResult();
        $handles = array();
        foreach($result as $r) {
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
        $upgradeables = array();
        foreach ($packages as $p) {
            if (version_compare($p->getPackageVersion(), $p->getPackageVersionUpdateAvailable(), '<')) {
                $upgradeables[] = $p;
            }
        }

        return $upgradeables;
    }


    /**
     * Returns a package's class.
     * @param string $pkgHandle Handle of package
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
                $cl = $this->application->make('Concrete\Core\Package\BrokenPackage', array($pkgHandle));
            }
            $item->set($cl);
        }

        return clone $cl;
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

}
