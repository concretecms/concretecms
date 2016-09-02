<?php
namespace Concrete\Tests\Core\Job;

use Concrete\Core\Job\Job;

class JobTest extends Base
{
    /** @test */
    public function set_job_status_to_running()
    {
        $job = $this->service->installByHandle('index_search')
            ->setJobStatus(Job::JOB_STATUS_RUNNING);

        $this->assertTrue($job->getJobStatus() == Job::JOB_STATUS_RUNNING);
    }

    /** @test */
    public function reset_job_to_enabled()
    {
        $job = $this->service->installByHandle('index_search')
            ->setJobStatus(Job::JOB_STATUS_DISABLED);

        $job->reset();

        $this->assertTrue($job->getJobStatus() == Job::JOB_STATUS_ENABLED);
    }

    /** @test */
    public function mark_job_as_started()
    {
        $job = $this->service->installByHandle('index_search');

        $this->assertEquals($job->getJobStatus(), Job::JOB_STATUS_ENABLED);

        $job->markStarted();

        $this->assertEquals($job->getJobStatus(), Job::JOB_STATUS_RUNNING);
    }

    /** @test */
    public function mark_job_as_completed()
    {
        $job = $this->service->installByHandle('index_search')
            ->markStarted();
        $this->assertEquals($job->getJobStatus(), Job::JOB_STATUS_RUNNING);

        $job->markCompleted();
        $this->assertEquals($job->getJobStatus(), Job::JOB_STATUS_ENABLED);
    }

    /** @test */
    public function execute_job()
    {
        $db = $this->app['database']->connection();
        $numberOfLogs = $db->fetchColumn("SELECT COUNT(1) FROM JobsLog");
        $this->assertEquals($numberOfLogs, 0);

        $job = $this->service->installByHandle('index_search');
        $this->assertEquals($job->getJobStatus(), Job::JOB_STATUS_ENABLED);

        $job->executeJob();
        $this->assertEquals($job->getJobStatus(), Job::JOB_STATUS_ENABLED);

        $numberOfLogs = $db->fetchColumn("SELECT COUNT(1) FROM JobsLog");
        $this->assertEquals($numberOfLogs, 1);
    }

    /** @test */
    public function set_job_schedule()
    {
        $job = $this->service->installByHandle('index_search')
            ->setSchedule(1, 'minutes', 45);

        $this->assertEquals(1, $job->isScheduled());

        $this->assertTrue($job->isScheduledForNow());

        $job->setDateLastRun(date('Y-m-d g:i:s A'));
        $this->assertFalse($job->isScheduledForNow());
    }
}
