<?php

namespace Concrete\Core\Database;

use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Package\PackageList;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Config;
use Database;
use Events;

class EntityManagerFactory implements EntityManagerFactoryInterface
{

    /**
     * Contains the cached AnnotationReader which is used by all packages
     * using Doctrine entities with mapping information as annotations with
     * a concrete version higher than 8.0.0
     * 
     * @var \Doctrine\Common\Annotations\CachedReader 
     */
    protected $cachedAnnotationsReader;

    /**
     * Contains the cached SimpleAnnotationReader
     * which is used by all packages using Doctrine entities with annotations
     * with a concrete version less than 8.0.0
     * 
     * @var \Doctrine\Common\Annotations\CachedReader 
     */
    protected $cachedSimpleAnnotationsReader;

    /**
     * Create EntityManager
     * 
     * @param Connection $connection
     * @return \Doctrine\ORM\EntityManager
     */
    public function create(Connection $connection)
    {

        // Set cache based on doctrine dev mode
        $isDevMode = Config::get('concrete.cache.doctrine_dev_mode');

        //@todo - test remove
        $isDevMode = true;

        if ($isDevMode) {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        } else {
            $cache = new DoctrineCacheDriver('cache/expensive');
        }

        // Create config
        $configuration = \Doctrine\ORM\Tools\Setup::createConfiguration(
                        $isDevMode, Config::get('database.proxy_classes'), $cache
        );

        // Register the doctrine Annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(DIR_BASE_CORE . '/vendor/doctrine/orm/lib/Doctrine/ORM' . '/Mapping/Driver/DoctrineAnnotations.php');
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('Application\Src', DIR_BASE . '/application/src');

        // Ignore the Annotions of the SimpleAnnotationReader
        // Ther is a bug in the Doctrine annotation doc parser class. 
        // If there's a class named the same as a annotation, an exeption will be raised. In the case of Concrete5 this is the [at]package annotaton
        // Even though the addGlobalIgnoredName is set the exception ist still thrown.
        // http://stackoverflow.com/questions/21609571/swagger-php-and-doctrine-annotation-issue
        // @Todo remove all unkown annotations used by the SimpleAnnotationReader form the AnnotationReader
        // to prevent fatal errors
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('subpackages');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('package');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Id');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Table');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Column');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('GeneratedValue');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Entity');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('OneToMany');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('ManyToOne');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('JoinColumn');

        // initiate the driver chain which will hold all driver instances
        $driverChain = new \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain();

        //@todo - > get lookup paths
        // Create the annotation reader > 8.0.0
        $annotationReader = new \Doctrine\Common\Annotations\AnnotationReader();
        $cachedAnnotationReader = new \Doctrine\Common\Annotations\CachedReader($annotationReader, $cache);
        // Add the reader to the DI container, so the reader same reader instance
        // can be accessed in the PackageService
        \Core::bind('orm/cachedAnnotationReader', function ($cachedAnnotationReader) {
            return $cachedAnnotationReader;
        });
        $this->cachedAnnotationReader = $cachedAnnotationReader;

        // Create legacy annotation reader used package requiring concrete5
        // version lower than 8.0.0
        $simpleAnnotationReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $simpleAnnotationReader->addNamespace('Doctrine\ORM\Mapping');
        $cachedSimpleAnnotationReader = new \Doctrine\Common\Annotations\CachedReader($simpleAnnotationReader, $cache);
        // Add the reader to the DI container, so the reader same reader instance
        // can be accessed in the PackageService
        \Core::bind('orm/cachedSimpleAnnotationReader', function ($cachedSimpleAnnotationReader) {
            return $cachedSimpleAnnotationReader;
        });
        $this->cachedSimpleAnnotationReader = $cachedSimpleAnnotationReader;

        // Create Core annotationDriver
        $coreDirs = array(
            DIR_BASE_CORE . DIRECTORY_SEPARATOR . DIRNAME_CLASSES,
        );
        $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($cachedAnnotationReader, $coreDirs);

        // @todo important -> test if defaultDriver can be used
        //$driverChain->setDefaultDriver($annotationDriver);
        //@todo not sure if is needed
        //$annotationDriver->addExcludePaths(Config::get('database.proxy_exclusions', array()));     
        $driverChain->addDriver($annotationDriver, 'Concrete\Core');

        // @todo important -> test if defaultDriver can be used
        //$driverChain->setDefaultDriver($annotationDriver);
        // Register application metadata driver;
        $this->addApplicationMetadataDriverToDriverChain($driverChain);

        // Register all concrete packages with entities to the driverChain 
        $this->addPackageMetadataDriverToDriverChain($driverChain);

//        \Doctrine\Common\Util\Debug::dump($driverChain);
//        die('ups');
//        
        // @todo - Provide a way to register the own doctrine extentions
        //       - add a way to register the gedmo extentions
        // Register Gedmo Doctrine Extensions 
        // 1.) AbstractMapping vs Mapping @See https://github.com/Atlantic18/DoctrineExtensions/issues/790
        //\Gedmo\DoctrineExtensions::registerMappingIntoDriverChainORM($driverChain, $cachedAnnotationReader);
        //\Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM($driverChain, $cachedAnnotationReader);
        // Inject DriverChain into the doctrine config
        $configuration->setMetadataDriverImpl($driverChain);

        // Get ORM event manager
        $eventManager = $connection->getEventManager();

        // Pass the database connection, the orm config and the event manager 
        // to the concrete5 event system so packages can hook in to the process
        // and alter the values
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('connection', $connection);
        $event->setArgument('configuration', $configuration);
        $event->setArgument('eventManager', $eventManager);
        Events::dispatch('on_entity_manager_configure', $event);

        // Reasign the values from the dispatched event
        $conn = $event->getArgument('connection');
        $config = $event->getArgument('configuration');
        $evm = $event->getArgument('eventManager');

        // @todo - test remove
//        \Doctrine\Common\Util\Debug::dump($configuration->getMetadataDriverImpl());
//        die('est');
        // Inject the ORM EventManager into the EntityManager so ORM Events
        // can be triggered.
        return EntityManager::create($conn, $config, $evm);
    }

    /**
     * Register the application metadata driver into the driver chain
     * Default metadata driver is the annotation driver
     * 
     * Other metadata driver typs (xml and yaml) can be configured in the configruation file:
     * application\config\concrete.php
     * 
     * 'application' => array('metadatadriver' => 'yaml'),
     * 
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    protected function addApplicationMetadataDriverToDriverChain($driverChain)
    {
        $appSrcPath = DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES;
        $xmlConfig = DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_XML;
        $ymlConfig = DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML;

        $appDriverSettings = \Config::get(CONFIG_ORM_METADATA_APPLICATION);

        if (empty($appDriverSettings)) {
            $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($this->cachedAnnotationsReader, $appSrcPath);
            $driverChain->addDriver($annotationDriver, 'Application\Src');
        } else if ($appDriverSettings === \Package::PACKAGE_METADATADRIVER_XML || $appDriverSettings === 'xml') {
            $xmlDriver = new \Doctrine\ORM\Mapping\Driver\XmlDriver($xmlConfig);
            $driverChain->addDriver($xmlDriver, 'Application\Src');
        } else if ($appDriverSettings === \Package::PACKAGE_METADATADRIVER_YAML || $appDriverSettings === 'yaml' || $appDriverSettings === 'yml') {
            $yamlDriver = new \Doctrine\ORM\Mapping\Driver\YamlDriver($ymlConfig);
            $driverChain->addDriver($yamlDriver, 'Application\Src');
        }
    }

    /**
     * Register all metadatadrivers of all installed packages containing entities 
     * into the driver chain
     * 
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    protected function addPackageMetadataDriverToDriverChain($driverChain)
    {
        $this->registerPkgWithAnnotationMetadataImpl($driverChain);
        $this->registerPkgWithXMLMetadataImpl($driverChain);
        $this->registerPkgWithYamlMetadataImpl($driverChain);
    }

    /**
     * Register the namespace and the metadata paths of all 
     * packages with annotations as ORM mapping information
     * 
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    protected function registerPkgWithAnnotationMetadataImpl($driverChain)
    {
        $driverSettingsLegacy = \Config::get(CONFIG_ORM_METADATA_ANNOTATION_LEGACY);
        $driverSettingsDefault = \Config::get(CONFIG_ORM_METADATA_ANNOTATION_DEFAULT);

        // add Annotation drivers with "legacy" Annotation reader
        if (count($driverSettingsLegacy) > 0) {
            foreach ($driverSettingsLegacy as $setting) {
                $simpleAnnotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($this->cachedSimpleAnnotationReader, $setting['paths']);
                $driverChain->addDriver($simpleAnnotationDriver, $setting['namespace']);
            }
        }

        // add Annotation drivers with normal Annotation reader -> Annotation prefixed with \ORM
        if (count($driverSettingsDefault) > 0) {
            foreach ($driverSettingsDefault as $setting) {
                $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($this->cachedAnnotationReader, $setting['paths']);
                $driverChain->addDriver($annotationDriver, $setting['namespace']);
            }
        }
    }

    /**
     * Register the namespace and the metadata paths of all 
     * packages with xml metadata as ORM mapping information
     * 
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    protected function registerPkgWithXMLMetadataImpl($driverChain)
    {
        $driverSettings = \Config::get(CONFIG_ORM_METADATA_XMLL);
        if (count($driverSettings) > 0) {
            foreach ($driverSettings as $setting) {
                $xmlDriver = new \Doctrine\ORM\Mapping\Driver\XmlDriver($setting['paths']);
                $driverChain->addDriver($xmlDriver, $setting['namespace']);
            }
        }
    }

    /**
     * Register the namespace and the metadata paths of all 
     * packages with yaml metadata as ORM mapping information
     * 
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    protected function registerPkgWithYamlMetadataImpl($driverChain)
    {
        $driverSettings = \Config::get(CONFIG_ORM_METADATA_YAML);
        if (count($driverSettings) > 0) {
            foreach ($driverSettings as $setting) {
                $yamlDriver = new \Doctrine\ORM\Mapping\Driver\YamlDriver($setting['paths']);
                $driverChain->addDriver($yamlDriver, $setting['namespace']);
            }
        }
    }

}
