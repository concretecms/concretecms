<?php
namespace Concrete\Core\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Package\ItemCategory\ItemInterface;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\Core\Page\Theme\Theme;
use Doctrine\ORM\EntityManagerInterface;

class PackageService
{

    protected $entityManager;
    protected $application;
    protected $localization;

    public function __construct(Localization $localization, Application $application, EntityManagerInterface $entityManager)
    {
        $this->application = $application;
        $this->localization = $localization;
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

    protected function clearEntityManagerMetadataCache()
    {

    }
    public function uninstall(Package $p)
    {
        $p->uninstall();
        $this->removPackageMetadataDriverFromConfig($p);
        $config = $this->entityManager->getConfiguration();
        $cache = $config->getMetadataCacheImpl();
        $cache->flushAll();
    }

    public function install(Package $p, $data)
    {
        $this->localization->pushActiveContext('system');
        try {
            if(!empty($p->getPackageMetadataPaths())){
                $config = $this->entityManager->getConfiguration();
                $driverChain = $config->getMetadataDriverImpl();

                $driver = $p->getMetadataDriver();
                $pkgNamespace = $p->getNamespace();

                $driverChain->addDriver($driver, $pkgNamespace);
                // add package metadata to application/config/database.php
                $this->savePackageMetadataDriverToConfig($p);
                
                $cache = $config->getMetadataCacheImpl();
                $cache->flushAll();
            }

            $u = new \User();
            $swapper = new ContentSwapper();
            $p->install($data);
            if ($u->isSuperUser() && $swapper->allowsFullContentSwap($p) && $data['pkgDoFullContentSwap']) {
                $swapper->swapContent($p, $data);
            }
            if (method_exists($p, 'on_after_swap_content')) {
                $p->on_after_swap_content($data);
            }
            $this->localization->popActiveContext();
            $pkg = $this->getByHandle($p->getPackageHandle());
            
            return $p;
        } catch (\Exception $e) {
            $this->localization->popActiveContext();
            $error = $this->application->make('error');
            $error->add($e);
            return $error;
        }
    }

    /**
     * Returns a package's class. Must be run statically because we can't use the injected entity manager
     * @param string $pkgHandle Handle of package
     * @return Package
     */
    public static function getClass($pkgHandle)
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
                $cl = $app->make('Concrete\Core\Package\BrokenPackage', array($pkgHandle));
            }
            $item->set($cl);
        }

        return clone $cl;
    }
    
    /**
     * Save the entity path of the package to the 
     * application/config/database.php
     * So the single entity manager is able to add the appropriate 
     * drivers for the package namespaces
     * 
     * @param \Concrete\Core\Package\Package $p
     */
    protected function savePackageMetadataDriverToConfig(Package $p)
    {
        $packageMetadataDriverType = $p->getMetadataDriverType();
        $packageHandle = $p->getPackageHandle();
        $config = $this->getFileConfigORMMetadata();

        $settings = $this->getPackageMetadataDriverSettings($p);
        
        if ($packageMetadataDriverType === Package::PACKAGE_METADATADRIVER_ANNOTATION) {
            if(version_compare($p->getApplicationVersionRequired(), '5.8.0', '<')){
                // Legacy - uses SimpleAnnotationReader
                $config->save(CONFIG_ORM_METADATA_ANNOTATION_LEGACY . '.' . strtolower($packageHandle), $settings);
            }else{
                // Use default AnnotationReader
                $config->save(CONFIG_ORM_METADATA_ANNOTATION_DEFAULT . '.' . strtolower($packageHandle), $settings);
            }
        } else if ($packageMetadataDriverType === Package::PACKAGE_METADATADRIVER_XML) {
            $config->save(CONFIG_ORM_METADATA_XML . '.' . strtolower($packageHandle), $settings);
        } else if ($packageMetadataDriverType === Package::PACKAGE_METADATADRIVER_YAML){
            $config->save(CONFIG_ORM_METADATA_YAML . '.' . strtolower($packageHandle), $settings);
        }
    }
        
    /**
     * Creates the default metadata driver settings array,
     * which is stored in the database config file
     * If the package has registerd any pkg autoloader namespaces,
     * these namespaces are merged into the settings
     * 
     * @param \Concrete\Core\Package\Package $p
     * @return array
     */
    protected function getPackageMetadataDriverSettings(Package $p){
        $settings[] = array(
            'namespace' => $p->getNamespace(),
            'paths' => $p->getPackageMetadataRelativePaths()
        );
        
        $additionalNamespaces = $p->getAdditionalNamespaces();
        
        if(count($additionalNamespaces)>0){
            $settings = array_merge($settings,$additionalNamespaces);
        }
        return $settings;
    }
    
    /**
     * Remove metadatadriver from config
     * 
     * @param \Concrete\Core\Package\Package $p
     */
    protected function removPackageMetadataDriverFromConfig(Package $p)
    {
        $packageMetadataDriverType = $p->getMetadataDriverType();
        $packageHandle = $p->getPackageHandle();
        $config = $this->getFileConfigORMMetadata();

        if ($packageMetadataDriverType === Package::PACKAGE_METADATADRIVER_ANNOTATION) {
            if(version_compare($p->getApplicationVersionRequired(), '5.8.0', '<')){
                // Legacy - uses SimpleAnnotationReader
                $basePath = CONFIG_ORM_METADATA_ANNOTATION_LEGACY;
            }else{
                // Use default AnnotationReader
                $basePath = CONFIG_ORM_METADATA_ANNOTATION_DEFAULT;
            }
        } else if ($packageMetadataDriverType === Package::PACKAGE_METADATADRIVER_XML) {
            $basePath = CONFIG_ORM_METADATA_XML;
        } else if ($packageMetadataDriverType === Package::PACKAGE_METADATADRIVER_YAML){
            $basePath = CONFIG_ORM_METADATA_YAML;
        }
        
        // $config->clear($basePath) does not remove settings in config files
        $metaDriverConfig = $config->get($basePath);
        unset($metaDriverConfig[strtolower($packageHandle)]);
        $config->save($basePath, $metaDriverConfig);
    }
    
    /**
     * Get the config with a direct file safer,
     * so settings can be saved directly to application/config/database.php
     * instead of application/config/generated_overrides
     * 
     * Used to store the orm metadata of packages
     * 
     * @return \Concrete\Core\Package\Repository
     */
    public function getFileConfigORMMetadata()
    {
        $defaultEnv = \Config::getEnvironment();
        $fileSystem = new \Illuminate\Filesystem\Filesystem();
        $fileLoader = new \Concrete\Core\Config\FileLoader($fileSystem);
        $directFileSaver = new \Concrete\Core\Config\DirectFileSaver($fileSystem);
        $repository = new \Concrete\Core\Config\Repository\Repository($fileLoader, $directFileSaver, $defaultEnv);
        return $repository;
    }
}
