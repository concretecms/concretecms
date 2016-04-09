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
     * using Doctrine entities with annotations as mapping information for
     * a concrete version > 8.0.0
     * 
     * @var \Doctrine\Common\Annotations\CachedReader 
     */
    protected $cachedAnnotationsReader;

    /**
     * Contains the cached SimpleAnnotationReader
     * which is used by all packages using doctrine entities with annotations
     * for a concrete version less than 8.0.0
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

        // Remove all unkown annotations from the AnnotationReader used by the SimpleAnnotationReader 
        // to prevent fatal errors
        $this->registerGlobalIgnoredAnnotations();

        // initiate the driver chain which will hold all driver instances
        $driverChain = new \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain();

        // Create the annotation reader used by packages and core > c5 version 8.0.0
        $annotationReader = new \Doctrine\Common\Annotations\AnnotationReader();
        $cachedAnnotationReader = new \Doctrine\Common\Annotations\CachedReader($annotationReader, $cache);
        $this->cachedAnnotationReader = $cachedAnnotationReader;

        // Create legacy annotation reader used package requiring concrete5
        // version lower than 8.0.0
        $simpleAnnotationReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $simpleAnnotationReader->addNamespace('Doctrine\ORM\Mapping');
        $cachedSimpleAnnotationReader = new \Doctrine\Common\Annotations\CachedReader($simpleAnnotationReader, $cache);
        $this->cachedSimpleAnnotationReader = $cachedSimpleAnnotationReader;

        // Create Core annotationDriver
        $coreDirs = array(
            DIR_BASE_CORE . DIRECTORY_SEPARATOR . DIRNAME_CLASSES,
        );
        $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($cachedAnnotationReader, $coreDirs);

        // The default driver only kicks in, if no driver has been found for a specific namespace. 
        // In c5 this shouldn't be the case. If some problems occure with entity 
        // mapping uncommenting the following line maybe helps to fix them.
        //$driverChain->setDefaultDriver($annotationDriver);

        $annotationDriver->addExcludePaths(Config::get('database.proxy_exclusions', array()));     
        $driverChain->addDriver($annotationDriver, 'Concrete\Core');

        // Register application metadata driver
        $this->addApplicationMetadataDriverToDriverChain($driverChain);

        // Register all installed packages in the driverChain 
        $this->addPackageMetadataDriverToDriverChain($driverChain);

        // Inject the driverChain into the doctrine config
        $configuration->setMetadataDriverImpl($driverChain);

        // Get orm event manager
        $eventManager = $connection->getEventManager();

        // Pass the database connection, the orm config and the event manager 
        // to the concrete5 event system so packages can hook in to the process
        // and alter the values
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('connection', $connection);
        $event->setArgument('configuration', $configuration);
        $event->setArgument('eventManager', $eventManager);
        // This argument is used by doctrine extentsions, which need to register their custom annotations.
        $event->setArgument('cachedAnnotationReader', $cachedAnnotationReader);
        Events::dispatch('on_entity_manager_configure', $event);

        // Add the reader to the DI container, so the same reader instance
        // can be accessed in the PackageService
        \Core::bind('orm/cachedAnnotationReader', function ($cachedAnnotationReader) {
            return $cachedAnnotationReader;
        });
        \Core::bind('orm/cachedSimpleAnnotationReader', function ($cachedSimpleAnnotationReader) {
            return $cachedSimpleAnnotationReader;
        });

        // Reasign the values from the dispatched event
        $conn = $event->getArgument('connection');
        $config = $event->getArgument('configuration');
        $evm = $event->getArgument('eventManager');

        // Inject the orm eventManager into the entityManager so orm events
        // can be triggered.
        return EntityManager::create($conn, $config, $evm);
    }

    /**
     * Register the application metadata driver to the driver chain
     * Default metadata driver is the annotation driver.
     * 
     * Other metadata driver typs (xml and yaml) can be configured in the 
     * database configruation file: application\config\database.php
     * 
     * 'metadatadriver' => array('application' => 'yaml'),
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
     * Register all metadatadrivers of all installed packages
     * in the driver chain
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
     * packages with annotations
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
     * packages with xml metadata
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
     * packages with yaml metadata
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

    /**
     * Register globally ignored annotations
     */
    protected function registerGlobalIgnoredAnnotations()
    {
        // There is a bug in the Doctrine annotation DocParser class. 
        // If there's a class named the same as an annotation, an exeption will be raised. 
        // In the case of Concrete5 this is the \@package annotaton. Even though 
        // the addGlobalIgnoredName is set the exception is still thrown.
        // http://stackoverflow.com/questions/21609571/swagger-php-and-doctrine-annotation-issue
        // Solution 1: Add this fix to \Doctrine\Common\Annotations\DocParser and customize other Classes
        // https://github.com/bfanger/annotations/commit/1dfb5073061d3fe856c3b138286aa4c75120fcd3 
        // Solution 2: Comment all [at]package annotation found in concrete/src with a backslash. Example \@package
        // The annotations added to the global ignored namespace are still valid for the simple annotation reader
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('subpackages');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('package');

        // Default Doctrine annotations
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Column');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('ColumnResult');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Cache');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('ChangeTrackingPolicy');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('DiscriminatorColumn');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('DiscriminatorMap');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Entity');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('EntityResult');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('FieldResult');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('GeneratedValue');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('HasLifecycleCallbacks');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Id');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('InheritanceType');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('JoinColumn');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('JoinColumns');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('JoinTable');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('ManyToOne');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('ManyToMany');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('MappedSuperclass');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('NamedNativeQuery');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('OneToOne');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('OneToMany');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('OrderBy');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('PostLoad');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('PostPersist');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('PostRemove');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('PostUpdate');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('PrePersist');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('PreRemove');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('PreUpdate');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('SequenceGenerator');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('SqlResultSetMapping');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Table');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('UniqueConstraint');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Version');
    }
}
