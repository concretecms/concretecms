<?php
namespace Concrete\Core\Job;

use Concrete\Core\Application\Application;

class Service
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $jHandle
     * @param $pkg
     *
     * @return false|Job
     */
    public function installByPackage($jHandle, $pkg)
    {
        $className = $this->getClassName($jHandle, $pkg->getPackageHandle());
        if (!class_exists($className)) {
            return false;
        }

        $job = $this->app->make($className);
        $db = $this->app['database']->connection();

        $db->executeQuery("INSERT INTO Jobs (jName, jDescription, jDateInstalled, jNotUninstallable, jHandle, pkgID) VALUES (?, ?, ?, ?, ?, ?)", [
            $job->getJobName(),
            $job->getJobDescription(),
            $this->app->make('helper/date')->getOverridableNow(),
            0,
            $jHandle,
            $pkg->getPackageID(),
        ]);

        $jobEvent = new Event($job);

        $this->app['director']->dispatch('on_job_install', $jobEvent);

        $job->jID = $db->lastInsertId();

        return $job;
    }

    /**
     * @param string $jHandle
     *
     * @param array $notInstalledJobs
     * @return false|Job
     */
    public function installByHandle($jHandle = '', $notInstalledJobs = [])
    {
        if (count($notInstalledJobs) === 0) {
            $includeC5Dirs = 1;
            $notInstalledJobs = $this->app->make('job')->getNotInstalledJobs($includeC5Dirs);
        }

        if (count($notInstalledJobs)) {
            foreach ($notInstalledJobs as $jobHandle => $jobObject) {
                if ($jobHandle != $jHandle) {
                    continue;
                }

                return $jobObject->install();
            }
        }

        return false;
    }

    /**
     * Removes Job log entries.
     */
    public function clearLog()
    {
        $db = $this->app['database']->connection();
        $db->query("TRUNCATE JobsLog");
    }

    /**
     * @param bool $jobToken
     * @return string
     */
    public function generateAuth($jobToken = false)
    {
        $jobToken = $jobToken ?: $this->app->make('config/database')->get('concrete.security.token.jobs');
        $val = $jobToken . ':' . DIRNAME_JOBS;

        return md5($val);
    }

    /**
     * @param $xml
     * @return null
     */
    public function exportList($xml)
    {
        $jobs = $this->getList();
        if (count($jobs) === 0) {
            return null;
        }

        $jx = $xml->addChild('jobs');
        foreach ($jobs as $j) {
            $ch = $jx->addChild('job');
            $ch->addAttribute('handle', $j->getJobHandle());
            $ch->addAttribute('package', $j->getPackageHandle());
        }
    }
}