<?php
namespace Concrete\Tests\Core\Job;

use Concrete\Core\Job\JobFactory;
use Concrete\Core\Job\Job;

class JobFactoryTest extends Base
{
    /** @test */
    public function install_by_handle_deprecated()
    {
        $job = Job::installByHandle('generate_sitemap');

        $this->assertInstanceOf(Job::class, $job);
    }

    /** @test */
    public function service_provider()
    {
        $factory = $this->app->make('job');

        $this->assertInstanceOf(JobFactory::class, $factory);
    }

    /** @test */
    public function get_list_of_jobs()
    {
        $this->app['database']->connection()->query('TRUNCATE Jobs');
        $this->service->install('index_search');
        $this->service->install('generate_sitemap');

        $jobs = $this->factory->installed();

        $this->assertCount(2, $jobs);
    }

    /** @test */
    public function get_list_of_available_jobs_deprecated()
    {
        $includeConcreteDir = true;
        $available_jobs = Job::getAvailableList($includeConcreteDir);

        $this->assertNotEmpty($available_jobs);
    }

    /** @test */
    public function get_list_of_scheduled_jobs()
    {
        $this->app['database']->connection()->query('TRUNCATE Jobs');

        $this->service->install('index_search');
        $this->service->install('generate_sitemap')
            ->setIsScheduled();

        $jobs = $this->factory->scheduled();

        $this->assertCount(1, $jobs);
    }

    /** @test */
    public function get_list_of_not_installed_jobs()
    {
        $jobs = $this->factory->getNotInstalledJobs();

        $this->assertNotNull($jobs);
    }

    /**
     * @test
     *
     * @param string $handle
     */
    public function get_job_by_handle($handle = 'index_search')
    {
        $this->service->install($handle);
        $job = $this->factory->getByHandle($handle);

        $this->assertInstanceOf(Job::class, $job);
    }

    /** @test */
    public function get_non_existing_job_by_handle()
    {
        $job = $this->factory->getByHandle('non_existing_job_handle');

        $this->assertNull($job);
    }

    /** @test */
    public function get_non_existing_job_by_id()
    {
        $job = $this->factory->getByID(99999999);

        $this->assertNull($job);
    }
}
