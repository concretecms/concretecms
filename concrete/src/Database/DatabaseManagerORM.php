<?php

namespace Concrete\Core\Database;

use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManager;

class DatabaseManagerORM
{
    /**
     * The application instance.
     *
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    protected $entityManager;

    /**
     * Create a new database ORM manager instance.
     *
     * @param \Concrete\Core\Application\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function entityManager()
    {
        return $this->app->make(EntityManager::class);
    }
}
