<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Cache\Adapter\DoctrineCacheDriver;
use Concrete\Core\Database\EntityManagerFactoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Config;

/**
 * Class EntityManagerFactory
 * @package Concrete\Core\Express
 * The backend entity manager hooks into Doctrine and is called by the front-end
 * entity manager.
 */
class ObjectManager
{

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function create($entityName)
    {
        $manager = $this->application->make('express.backend');
        $entity = $manager->find($entityName);
        var_dump_safe($entity);
    }

}
