<?php

namespace Concrete\Core\Database;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Database\EntityManager\Driver\ApplicationDriver;
use Concrete\Core\Database\EntityManager\Driver\CoreDriver;

/**
 * EntityManagerConfigFactory
 * Responsible for bootstrapping the core concrete5 entity manager (Concrete\Core\Entity) and the application level
 * entity manager. Sets the stage for the package entity manager once its time for them to come online.
 * @author markus.liechti
 * @author Andrew Embler
 */
class EntityManagerConfigFactory implements ApplicationAwareInterface, EntityManagerConfigFactoryInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Doctrine ORM config
     *
     * @var \Doctrine\ORM\Configuration
     */
    protected $configuration;

    /**
     * Concrete5 configuration files repository
     *
     * @var \Illuminate\Config\Repository or \Concrete\Core\Config\Repository\Repository
     */
    protected $configRepository;

    /**
     * Constructor
     */
    public function __construct(
        \Concrete\Core\Application\Application $app,
        \Doctrine\ORM\Configuration $configuration,
        \Illuminate\Config\Repository $configRepository
    ) {
        $this->setApplication($app);
        $this->configuration = $configuration;
        $this->configRepository = $configRepository;
    }

    /**
     * @param \Concrete\Core\Application\Application $application
     */
    public function setApplication(\Concrete\Core\Application\Application $application)
    {
        $this->app = $application;
    }

    /**
     * Set configRepository
     *
     * @param \Illuminate\Config\Repository $configRepository
     */
    public function setConfigRepository(\Illuminate\Config\Repository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * Get configRepository
     *
     * @return \Illuminate\Config\Repository
     */
    public function getConfigRepository()
    {
        return $this->configRepository;
    }

    /**
     * Add driverChain and get orm config
     *
     * @return \Doctrine\ORM\Configuration
     */
    public function getConfiguration()
    {
        $driverChain = $this->getMetadataDriverImpl();
        // Inject the driverChain into the doctrine config
        $this->configuration->setMetadataDriverImpl($driverChain);
        return $this->configuration;
    }

    /**
     *
     * @return \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain
     */
    public function getMetadataDriverImpl()
    {
        // Register the doctrine Annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(DIR_BASE_CORE . '/vendor/doctrine/orm/lib/Doctrine/ORM' . '/Mapping/Driver/DoctrineAnnotations.php');

        $legacyNamespace = $this->getConfigRepository()->get('app.enable_legacy_src_namespace');
        if ($legacyNamespace) {
            \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('Application\Src',
                DIR_BASE . '/application/src');
        } else {
            \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('Application\Entity',
                DIR_BASE . '/application/src/Entity');
        }
        // Remove all unkown annotations from the AnnotationReader used by the SimpleAnnotationReader
        // to prevent fatal errors
        $this->registerGlobalIgnoredAnnotations();

        // initiate the driver chain which will hold all driver instances
        $driverChain = $this->app->make('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain');

        $coreDriver = new CoreDriver($this->app);
        $driver = $coreDriver->getDriver();
        $driver->addExcludePaths($this->getConfigRepository()->get('database.proxy_exclusions', array()));
        $driverChain->addDriver($driver, $coreDriver->getNamespace());

        // Register application metadata driver
        $config = $this->getConfigRepository();
        $applicationDriver = new ApplicationDriver($config, $this->app);
        $driver = $applicationDriver->getDriver();
        if (is_object($driver)) {
            // $driver might be null, if there's no application/src/Entity
            $driverChain->addDriver($applicationDriver->getDriver(), $applicationDriver->getNamespace());
        }

        return $driverChain;
    }


    /**
     * Register all metadatadrivers of all installed packages
     * in the driver chain
     *
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    /*
    protected function addPackageMetadataDriverToDriverChain($driverChain)
    {
        $this->registerPkgWithAnnotationMetadataImpl($driverChain);
        $this->registerPkgWithXMLMetadataImpl($driverChain);
        $this->registerPkgWithYamlMetadataImpl($driverChain);
    }
    */

    /**
     * Register the namespace and the metadata paths of all
     * packages with annotations
     *
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    /*
    protected function registerPkgWithAnnotationMetadataImpl($driverChain)
    {
        $driverSettingsLegacy  = $this->getConfigRepository()->get(CONFIG_ORM_METADATA_ANNOTATION_LEGACY);
        $driverSettingsDefault = $this->getConfigRepository()->get(CONFIG_ORM_METADATA_ANNOTATION_DEFAULT);

        // add Annotation drivers with "legacy" Annotation reader
        if (count($driverSettingsLegacy) > 0) {
            foreach ($driverSettingsLegacy as $settings) {
                foreach ($settings as $setting) {
                    $paths = $this->convertRelativeToAbsolutePaths($setting);
                    $hasInvalidPaths = $this->hasInvalidPaths($paths);
                    if ($hasInvalidPaths) {
                        // At least one mapping path is invalid
                        continue;
                    }
                    $simpleAnnotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($this->getCachedSimpleAnnotationReader(),
                        $paths);
                    $driverChain->addDriver($simpleAnnotationDriver,
                        $setting['namespace']);
                }
            }
        }

        // add Annotation drivers with normal Annotation reader -> Annotation prefixed with \ORM
        if (count($driverSettingsDefault) > 0) {
            foreach ($driverSettingsDefault as $settings) {
                foreach($settings as $setting){
                    $paths = $this->convertRelativeToAbsolutePaths($setting);
                    $hasInvalidPaths = $this->hasInvalidPaths($paths);
                    if ($hasInvalidPaths) {
                        // At least one mapping path is invalid
                        continue;
                    }
                    $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($this->getCachedAnnotationReader(),
                        $paths);
                    $driverChain->addDriver($annotationDriver, $setting['namespace']);
                }
            }
        }
    }
       */
    /**
     * Register the namespace and the metadata paths of all
     * packages with xml metadata
     *
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    /*
    protected function registerPkgWithXMLMetadataImpl($driverChain)
    {
        $driverSettings = $this->getConfigRepository()->get(CONFIG_ORM_METADATA_XML);
        if (count($driverSettings) > 0) {
            foreach ($driverSettings as $settings) {
                foreach($settings as $setting){
                    $paths = $this->convertRelativeToAbsolutePaths($setting);
                    $hasInvalidPaths = $this->hasInvalidPaths($paths);
                    if ($hasInvalidPaths) {
                        // At least one mapping path is invalid
                        continue;
                    }
                    $xmlDriver = new \Doctrine\ORM\Mapping\Driver\XmlDriver($paths);
                    $driverChain->addDriver($xmlDriver, $setting['namespace']);
                }
            }
        }
    }*/


    /**
     * Register the namespace and the metadata paths of all
     * packages with yaml metadata
     *
     * @param \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $driverChain
     */
    /*
    protected function registerPkgWithYamlMetadataImpl($driverChain)
    {
        $driverSettings = $this->getConfigRepository()->get(CONFIG_ORM_METADATA_YAML);
        if (count($driverSettings) > 0) {
            foreach ($driverSettings as $settings) {
                foreach($settings as $setting){
                    $paths = $this->convertRelativeToAbsolutePaths($setting);
                    $hasInvalidPaths = $this->hasInvalidPaths($paths);
                    if ($hasInvalidPaths) {
                        // At least one mapping path is invalid
                        continue;
                    }
                    $yamlDriver = new \Doctrine\ORM\Mapping\Driver\YamlDriver($paths);
                    $driverChain->addDriver($yamlDriver, $setting['namespace']);
                }
            }
        }
    }*/

    /**
     * Checks if the mapping paths are valid. If a namespace contains atleast one
     * invalid mapping path the method returns true.
     * If all is fine, false is returned
     *
     * @param array $paths
     * @return boolean
     */
    /*
    protected function hasInvalidPaths (array $paths){
        
        $oneInvalidPath = false;
        
        if (count($paths) > 0){
            foreach ($paths as $path) {
                if (!is_dir($path)) {
                    // Is no directory set flag to true
                    $oneInvalidPath = true;
                }
            }
        }
        return $oneInvalidPath;
    }*/

    /**
     * Prepend all relativ paths with DIR_BASE
     *
     * @param array $setting
     */
    /*
    protected function convertRelativeToAbsolutePaths(array $setting)
    {
        $paths = $setting['paths'];
        $newPaths = array();
        if(count($paths) > 0){
            foreach($paths as $path){
                $newPaths[] = DIR_BASE.$path;
            }
        }
        return $newPaths;
    }*/

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

        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Embeddable');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Embedded');
    }
}