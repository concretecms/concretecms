<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\EntityManagerFactoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Config;

/**
 * Class EntityManagerFactory.
 *
 * @package Concrete\Core\Express
 * The backend entity manager hooks into Doctrine and is called by the front-end
 * object manager, which is what users actually engage with.
 */
class BackendEntityManagerFactory implements EntityManagerFactoryInterface
{
    protected $rootEntityManager;
    protected $application;
    protected $driver;

    public function __construct(Application $application, EntityManager $rootEntityManager, DoctrineMappingDriver $driver = null)
    {
        $this->application = $application;
        $this->driver = $driver;
        $this->rootEntityManager = $rootEntityManager;
    }

    public function getDriver()
    {
        if (!isset($this->driver)) {
            $this->driver = new \Concrete\Core\Express\DoctrineMappingDriver($this->application, $this->rootEntityManager);
            $this->driver->setNamespace($this->application['config']->get('express.entity_classes.namespace'));
        }

        return $this->driver;
    }

    public function getClassName(Entity $entity)
    {
        return $this->getDriver()->getNamespace() . '\\' . $entity->getName();
    }

    public function create(Connection $connection)
    {
        $config = Setup::createConfiguration(
            $this->application['config']->get('concrete.cache.doctrine_dev_mode'),
            $this->application['config']->get('database.proxy_classes'),
           new ArrayCache() // we don't want to cache because we always want to get the updated information
        );
        $strategy = new NamingStrategy($this->rootEntityManager);
        $strategy->setTablePrefix($this->application['config']->get('express.database.table_prefix'));

        $config->setNamingStrategy($strategy);
        $config->setMetadataDriverImpl($this->getDriver());
        $config->setClassMetadataFactoryName('Doctrine\ORM\Tools\DisconnectedClassMetadataFactory');

        return EntityManager::create($connection, $config);
    }
}
