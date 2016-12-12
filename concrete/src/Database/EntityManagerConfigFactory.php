<?php

namespace Concrete\Core\Database;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Database\EntityManager\Driver\ApplicationDriver;
use Concrete\Core\Database\EntityManager\Driver\CoreDriver;
use Concrete\Core\Application\ApplicationAwareTrait;

/**
 * EntityManagerConfigFactory
 * Responsible for bootstrapping the core concrete5 entity manager (Concrete\Core\Entity) and the application level
 * entity manager. Sets the stage for the package entity manager once its time for them to come online.
 * @author markus.liechti
 * @author Andrew Embler
 */
class EntityManagerConfigFactory implements ApplicationAwareInterface, EntityManagerConfigFactoryInterface
{
    use ApplicationAwareTrait;

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
        \Doctrine\Common\Annotations\AnnotationRegistry::registerFile('doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');

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
            $driverChain->addDriver($driver, $applicationDriver->getNamespace());
        }

        return $driverChain;
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

        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Embeddable');
        \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('Embedded');
    }
}