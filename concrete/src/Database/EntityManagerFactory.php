<?php
namespace Concrete\Core\Database;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Package\Package;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Config;
use Database;
use Events;

class EntityManagerFactory implements EntityManagerFactoryInterface
{

    protected $entitiesPath;
    protected $context;

    public function __construct($entitiesPath, $context = null)
    {
        $this->entitiesPath = $entitiesPath;
        $this->context = null;
    }

    public function create(Connection $connection)
    {
        $config = Setup::createConfiguration(
            Config::get('concrete.cache.doctrine_dev_mode'),
            Config::get('database.proxy_classes'),
            new DoctrineCacheDriver('cache/expensive')
        );

        $driverImpl = $config->newDefaultAnnotationDriver($this->entitiesPath);
        $driverImpl->addExcludePaths(Config::get('database.proxy_exclusions', array()));
        $config->setMetadataDriverImpl($driverImpl);

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();
        $event->setArgument('connection', $connection);
        $event->setArgument('configuration', $config);
        $event->setArgument('context', $this->context);
        Events::dispatch('on_entity_manager_configure', $event);
        $config = $event->getArgument('configuration');

        return EntityManager::create($connection, $config);
    }

}
