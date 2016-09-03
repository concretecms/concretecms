<?php
namespace Concrete\Core\Job;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;

class Service
{
    protected $app;

    public function __construct(Application $app, JobFactory $jobFactory)
    {
        $this->app = $app;
        $this->factory = $jobFactory;
    }

    /**
     * @param Job|string $jobObjectOrHandle
     * @param Package|\SimpleXMLElement|null $pkg
     *
     * @return Job|false
     */
    public function install($jobObjectOrHandle, $pkg = null)
    {
        $job = $jobObjectOrHandle;

        if ($jobObjectOrHandle instanceof Job) {
            $job = $this->factory->getByHandle($job->getJobHandle());

            // Job is already installed
            if ($job) {
                return false;
            }
        }

        // The XML importer passes a SimpleXMLElement.
        // Cast it to a string to get the job handle.
        if ($jobObjectOrHandle instanceof \SimpleXMLElement) {
            $jobObjectOrHandle = (string) $jobObjectOrHandle;
        }

        if (is_string($jobObjectOrHandle)) {
            $job = $this->getNotInstalledJobByHandle($jobObjectOrHandle);
        }

        // Job doesn't exist, is already installed, or is of a wrong type.
        if (!$job || !$job instanceof Job) {
            return false;
        }

        /** @var Connection $db */
        $db = $this->app['database']->connection();

        if ($pkg) {
            $job->setPackageID($pkg->getPackageID());
        }

        $db->executeQuery("INSERT INTO Jobs
          (jName, jDescription, jDateInstalled, jHandle, pkgID)
          VALUES (?, ?, ?, ?, ?)", [
            $job->getJobName(),
            $job->getJobDescription(),
            $this->app->make('helper/date')->getOverridableNow(),
            $job->getJobHandle(),
            $job->getPackageID(),
        ]);

        $job->jID = $db->lastInsertId();

        $jobEvent = new Event($job);
        $this->app['director']->dispatch('on_job_install', $jobEvent);

        return $job;
    }

    /**
     * Returns an instance of the job if it has not been installed yet.
     *
     * @param string $jHandle
     *
     * @return Job|bool false if not found
     */
    public function getNotInstalledJobByHandle($jHandle)
    {
        $includeC5Dirs = 1;
        $notInstalledJobs = $this->factory->getNotInstalledJobs($includeC5Dirs);

        // All available jobs are installed already
        if (count($notInstalledJobs) === 0) {
            return false;
        }

        foreach ($notInstalledJobs as $jobHandle => $jobObject) {
            if ($jHandle !== $jobHandle) {
                continue;
            }

            return $jobObject;
        }

        return false;
    }

    /**
     * Removes Job log entries.
     */
    public function clearLog()
    {
        $db = $this->app['database']->connection();
        $db->query('TRUNCATE JobsLog');
    }

    /**
     * @param string|null $jobToken
     *
     * @return string
     */
    public function generateAuth($jobToken = null)
    {
        $jobToken = $jobToken ?: $this->app->make('config/database')->get('concrete.security.token.jobs');
        $val = $jobToken.':'.DIRNAME_JOBS;

        return md5($val);
    }

    /**
     * @param $xml
     */
    public function exportList($xml)
    {
        $jobs = $this->getList();
        if (count($jobs) === 0) {
            return;
        }

        $jx = $xml->addChild('jobs');
        foreach ($jobs as $j) {
            $ch = $jx->addChild('job');
            $ch->addAttribute('handle', $j->getJobHandle());
            $ch->addAttribute('package', $j->getPackageHandle());
        }
    }
}
