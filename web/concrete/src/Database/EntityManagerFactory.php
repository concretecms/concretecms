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
    /* 
     * old create function
     * @todo - remove
     * 
     */
    public function create2(Connection $connection)
    {
        $config = Setup::createConfiguration(
            Config::get('concrete.cache.doctrine_dev_mode'),
            Config::get('database.proxy_classes'),
            new DoctrineCacheDriver('cache/expensive')
        );

        $driverImpl = $config->newDefaultAnnotationDriver(array(
            DIR_BASE_CORE . '/' . DIRNAME_CLASSES,
        ));
        $driverImpl->addExcludePaths(Config::get('database.proxy_exclusions', array()));
        $config->setMetadataDriverImpl($driverImpl);

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('connection', $connection);
        $event->setArgument('configuration', $config);
        Events::dispatch('on_entity_manager_configure', $event);
        $config = $event->getArgument('configuration');

        return EntityManager::create($connection, $config);
    }
    
    /**
     * Create EntityManager
     * 
     * @param Connection $connection
     * @return \Doctrine\ORM\EntityManager
     */
    public function create(Connection $connection) {
        
        // Set cache based on doctrine dev mode
        $isDevMode = Config::get('concrete.cache.doctrine_dev_mode');
        
        //@todo - test remove
        $isDevMode = true;
        
        if($isDevMode){
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }else{
            $cache = new DoctrineCacheDriver('cache/expensive');
        }
        
        // Create config
        $configuration = \Doctrine\ORM\Tools\Setup::createConfiguration(
            $isDevMode,
            Config::get('database.proxy_classes'),
            $cache
        );
        
        // Register the doctrine Annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerFile(DIR_BASE_CORE . '/vendor/doctrine/orm/lib/Doctrine/ORM' .'/Mapping/Driver/DoctrineAnnotations.php');  
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace('Application\Src', DIR_BASE.'/application/src');
        
        // Ignore the Annotions of the SimpleAnnotationReader
        // Ther is a bug in the Doctrine annotation doc parser class. 
        // If there's a class named the same as a annotation, an exeption will be raised. In the case of Concrete5 this is the [at]package annotaton
        // Even though the addGlobalIgnoredName is set the exception ist still thrown.
        // http://stackoverflow.com/questions/21609571/swagger-php-and-doctrine-annotation-issue
        
        // @Todo remove all possible annototaions
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
        $searchDirs = array(
            DIR_BASE_CORE . '/' . DIRNAME_CLASSES,
        );
        // Create the annotation reader > 8.0.0
        $annotationReader = new \Doctrine\Common\Annotations\AnnotationReader();
        $cachedAnnotationReader = new \Doctrine\Common\Annotations\CachedReader($annotationReader, $cache);
        \Core::bind('orm/cachedAnnotationReader', function($cachedAnnotationReader){
            return $cachedAnnotationReader;
        });
        $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($cachedAnnotationReader, $searchDirs);
        //@todo not sure if is needed
        $annotationDriver->addExcludePaths(Config::get('database.proxy_exclusions', array()));
        
        //@todo - > get namespaces form config file
        //          and add as many drivers as needed
        //          
        $driverChain->addDriver($annotationDriver, 'Concrete\Core');
        // @todo -> test if defaultDriver can be used
        //$driverChain->setDefaultDriver($annotationDriver);
        

        // @todo -> get lookup paths
        $simpleSearchDirs = array();
        // Create legacy annotation reader used package requiring concrete5
        // version lower than 8.0.0
        $simpleAnnotationReader = new \Doctrine\Common\Annotations\SimpleAnnotationReader();
        $simpleAnnotationReader->addNamespace('Doctrine\ORM\Mapping');
        $cachedSimpleAnnotationReader = new \Doctrine\Common\Annotations\CachedReader($simpleAnnotationReader, $cache);
        \Core::bind('orm/cachedSimpleAnnotationReader', function($cachedSimpleAnnotationReader){
            return $cachedSimpleAnnotationReader;
        });
        //$simpleAnnotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver($cachedSimpleAnnotationReader, $simpleSearchDirs);
        //@todo not sure if is needed
        //$simpleAnnotationDriver->addExcludePaths(Config::get('database.proxy_exclusions', array()));
        //@todo - > get package namespaces form config file
        //          and add as many drivers as needed
        //$driverChain->addDriver($simpleAnnotationDriver, '');
        
        // @todo -> create for each registred namespace a yaml driver
        // Register yaml drivers
        $yamlDir = DIR_CONFIG_SITE.DIRECTORY_SEPARATOR.'yaml';
        $yamlDriver = new \Doctrine\ORM\Mapping\Driver\YamlDriver($yamlDir);
        //$driverChain->addDriver($yamlDriver, 'Application\Src');
        
        // @todo -> add xml driver 
        
        
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
        
        // Inject the ORM EventManager into the EntityManager so ORM Events
        // can be triggered.
        return EntityManager::create($conn, $config, $evm);
    }
}
