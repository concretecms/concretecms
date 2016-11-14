<?php
namespace Concrete\Tests\Core\Job;

use Concrete\Core\Job\Job;
use Events;

class ServiceTest extends Base
{
    /** @test */
    public function get_job_auth_token()
    {
        $jobTokenFromConfig = 'za6YUeORprnXYUjisytUyCjxAVemcBkjvpRX5cB15IJ9qeTIWMfIxHxlzvU3JidV';
        $token = $this->service->generateAuth($jobTokenFromConfig);

        $this->assertEquals(32, strlen($token));
    }

    /** @test */
    public function clear_log()
    {
        $this->service->clearLog();

        $db = $this->app['database']->connection();
        $numberOfLogs = $db->fetchColumn("SELECT COUNT(1) FROM JobsLog");

        $this->assertEquals(0, $numberOfLogs);
    }

    /**
     * @test
     *
     * @param string $handle
     */
    public function install_by_handle($handle = 'index_search')
    {
        $job = $this->service->install($handle);

        $this->assertInstanceOf(Job::class, $job);
    }

    /** @test */
    public function import_job_from_xml()
    {
        $xml = simplexml_load_string('<job handle="index_search"/>');
        $job = $this->service->install($xml['handle']);

        $this->assertInstanceOf(Job::class, $job);
    }

    /** @test */
    public function fire_job_execute_event()
    {
        $event_triggered = false;
        Events::addListener('on_job_execute', function ($e) use (&$event_triggered) {
            $event_triggered = true;
        });

        $this->service->install('index_search')
            ->executeJob();

        $this->assertTrue($event_triggered);
    }

    /** @test */
    public function fire_job_install_event()
    {
        $event_triggered = false;
        Events::addListener('on_job_install', function ($e) use (&$event_triggered) {
            $event_triggered = true;
        });

        $this->service->install('index_search');
        $this->assertTrue($event_triggered);
    }

    /** @test */
    public function uninstall_job()
    {
        $event_triggered = false;
        Events::addListener('on_job_uninstall', function ($e) use (&$event_triggered) {
            $event_triggered = true;
        });

        $job = $this->service->install('index_search');
        $this->service->uninstall($job);

        $this->assertTrue($event_triggered);

        $job = $this->factory->getByHandle('index_search');
        $this->assertNull($job);
    }
}
