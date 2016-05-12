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
     * Factory for bootstraping the doctrine orm configuration
     *
     * @var \Concrete\Core\Database\EntityManagerConfigFactoryInterface
     */
    protected $configFactory;

    /**
     * Constructor
     */
    public function __construct(\Concrete\Core\Database\EntityManagerConfigFactoryInterface $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    /**
     * Create EntityManager
     * 
     * @param Connection $connection
     * @return \Doctrine\ORM\EntityManager
     */
    public function create(Connection $connection)
    {
        // Get config with containing all metadata drivers
        $configuration = $this->configFactory->getConfiguration();

        // Get orm event manager
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
        $conn   = $event->getArgument('connection');
        $config = $event->getArgument('configuration');
        $evm    = $event->getArgument('eventManager');

        // Inject the orm eventManager into the entityManager so orm events
        // can be triggered.
        return EntityManager::create($conn, $config, $evm);
    }
}