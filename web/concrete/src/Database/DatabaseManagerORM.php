<?php
namespace Concrete\Core\Database;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Package\Package;
use Doctrine\ORM\EntityManager;
use Database;

class DatabaseManagerORM
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    protected $entityManager;

    /**
     * Create a new database ORM manager instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function entityManager()
    {
        return $this->app->make('Doctrine\ORM\EntityManager');
    }

}
