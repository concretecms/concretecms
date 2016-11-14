<?php
namespace Concrete\Tests\Core\Job;

use Concrete\Core\Application\Application;
use Concrete\Core\Job\JobFactory;
use Concrete\Core\Job\Service;
use ConcreteDatabaseTestCase;

class Base extends ConcreteDatabaseTestCase
{
    protected $tables = ['Jobs', 'JobsLog'];

    /** @var Application */
    protected $app;

    /** @var JobFactory */
    protected $factory;

    /** @var Service */
    protected $service;

    public function setUp()
    {
        $this->app = clone \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $this->app['config'] = clone $this->app['config'];

        $this->factory = new JobFactory($this->app);
        $this->service = new Service($this->app, $this->factory);

        parent::setUp();
    }

    public function tearDown()
    {
        $this->app = null;

        parent::tearDown();
    }
}
