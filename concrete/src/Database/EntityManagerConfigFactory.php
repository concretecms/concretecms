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
     * @return \Doctrine\Persistence\Mapping\Driver\MappingDriverChain
     */
    public function getMetadataDriverImpl()
    {
        // Register the doctrine Annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerUniqueLoader('class_exists');

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
     * Register globally ignored annotations.
     */
    protected function registerGlobalIgnoredAnnotations()
    {
        static::registerGlobalIgnoredPHPDocAnnotations();

        // Names of the Doctrine ORM annotations written without a namespace (for example "Column" instead of
        // "ORM\Column"), which is the style of the legacy entities read by the SimpleAnnotationReader.
        // When the standard AnnotationReader meets a class written in that style, those names are not imported by any
        // "use" statement: without ignoring them, the reader would throw a "never imported" AnnotationException
        // instead of simply considering that class as not mapped.
        // This doesn't affect the SimpleAnnotationReader: it resolves the names by using its own list of namespaces,
        // without checking the ignored names.
        foreach ([
            'Cache',
            'ChangeTrackingPolicy',
            'Column',
            'ColumnResult',
            'DiscriminatorColumn',
            'DiscriminatorMap',
            'Embeddable',
            'Embedded',
            'Entity',
            'EntityResult',
            'FieldResult',
            'GeneratedValue',
            'HasLifecycleCallbacks',
            'Id',
            'InheritanceType',
            'JoinColumn',
            'JoinColumns',
            'JoinTable',
            'ManyToMany',
            'ManyToOne',
            'MappedSuperclass',
            'NamedNativeQuery',
            'OneToMany',
            'OneToOne',
            'OrderBy',
            'PostLoad',
            'PostPersist',
            'PostRemove',
            'PostUpdate',
            'PrePersist',
            'PreRemove',
            'PreUpdate',
            'SequenceGenerator',
            'SqlResultSetMapping',
            'Table',
            'UniqueConstraint',
            'Version',
        ] as $name) {
            \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName($name);
        }
    }

    /**
     * Make the Doctrine annotation reader ignore the PHPDoc annotations it does not know.
     */
    public static function registerGlobalIgnoredPHPDocAnnotations()
    {
        // Doctrine already ignores the most common PHPDoc annotations (included "package"): here we add the other
        // ones that may be found in the classes read by the annotation readers.
        // Please remark that old versions of the DocParser class (before doctrine/annotations 1.2) didn't check the
        // ignored names when a class with the same name of the annotation exists (that's the case of "package"):
        // that's why some PHPDoc annotations in the core are still escaped with a backslash.
        foreach ([
            'package',
            'subpackages',
        ] as $name) {
            \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName($name);
        }
    }
}