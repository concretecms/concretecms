<?php
namespace Concrete\Core\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Package\PackageList;
use Illuminate\Filesystem\Filesystem;

class JobFactory implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * Return list of installed Jobs.
     * @return \Iterator|Job[]
     */
    public function installed()
    {
        $db = $this->app['database']->connection();
        $q = "SELECT jID FROM Jobs ORDER BY jDateLastRun, jID";
        $r = $db->query($q);

        while ($row = $r->fetchRow($q)) {
            $j = $this->getByID($row['jID']);
            if (is_object($j)) {
                yield $j;
            }
        }
    }

    /**
     * Return list of scheduled Jobs.
     * @return \Iterator|Job[]
     */
    public function scheduled()
    {
        $db = $this->app->make(Connection::class);
        $q = "SELECT jID FROM Jobs WHERE isScheduled = 1 ORDER BY jDateLastRun, jID";
        $r = $db->query($q);

        while ($row = $r->fetchRow($q)) {
            $j = $this->getByID($row['jID']);
            if (is_object($j)) {
                yield $j;
            }
        }
    }

    /**
     * Scan job directories for job classes.
     *
     * @param bool $includeConcreteDirJobs
     * @return \Iterator|Job[]
     */
    public function getNotInstalledJobs($includeConcreteDirJobs = true)
    {
        // Get existing jobs
        $existingJobHandles = [];
        $existingJobs = $this->installed();
        foreach ($existingJobs as $j) {
            $existingJobHandles[$j->getJobHandle()] = true;
        }

        $jobClassLocations = $this->getJobClassLocations($includeConcreteDirJobs);

        $file = $this->app->make(Filesystem::class);
        foreach ($jobClassLocations as $location) {
            // If the location doesn't exist, just continue
            if (!$file->exists($location) || !$file->isDirectory($location)) {
                continue;
            }

            // Get all files in the directory
            $files = $file->allFiles($location);
            foreach ($files as $fileInfo) {
                if (strtolower($file->extension($fileInfo)) == 'php') {
                    $handle = $file->name($fileInfo);

                    // If the job is already installed, lets skip it
                    if (isset($existingJobHandles[$handle])) {
                        continue;
                    }

                    if ($job = $this->populateJobObject($handle, [], false)) {
                        yield $handle => $job;
                    }
                }
            }
        }
    }

    /**
     * Get a job object by its ID
     * @param int $jID
     * @return Job|null
     */
    public function getByID($jID)
    {
        $db = $this->app['database']->connection();
        $jobData = $db->fetchAssoc("SELECT * FROM Jobs WHERE jID=?", [
            intval($jID),
        ]);

        if (!$jobData || !$jobData['jHandle']) {
            return null;
        }

        return $this->populateJobObject($jobData['jHandle'], $jobData);
    }

    /**
     * Get an installed job by handle
     * If you want to get a job that is not installed, use ->
     *
     * @param string $jHandle
     * @return Job|null
     */
    public function getByHandle($jHandle)
    {
        $db = $this->app['database']->connection();

        // Make sure that this job is installed
        $jobData = $db->fetchAssoc("SELECT * FROM Jobs WHERE jHandle=?", [
            $jHandle,
        ]);

        if (!$jobData || !$jobData['jHandle']) {
            return null;
        }

        return $this->populateJobObject($jobData['jHandle'], $jobData);
    }

    /**
     * Populate a job object given a handle
     * @param string $jHandle
     * @param array $jobData
     * @param bool $checkInstalled If this is false, populating package jobs won't work properly
     * @return \Concrete\Core\Job\Job|null
     */
    protected function populateJobObject($jHandle = '', $jobData = [], $checkInstalled = true)
    {
        $jcl = $this->getJobClassLocations();
        $pkgHandle = null;

        if ($checkInstalled) {
            //check for the job file in the various locations
            $db = $this->app['database']->connection();
            $pkgID = $db->fetchColumn("SELECT pkgID FROM Jobs WHERE jHandle = ?", [
                $jHandle,
            ]);

            if ($pkgID > 0) {
                $pkgHandle = PackageList::getHandle($pkgID);
                if ($pkgHandle) {
                    $jcl[] = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JOBS;
                    $jcl[] = DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_JOBS;
                }
            }
        }

        foreach ($jcl as $jobClassLocation) {
            //load the file & class, then run the job
            $path = $jobClassLocation . '/' . $jHandle . '.php';
            if (file_exists($path)) {
                $className = $this->getClassName($jHandle, $pkgHandle);

                $j = $this->app->make($className);
                $j->jHandle = $jHandle;
                if (count($jobData)) {
                    $j->setPropertiesFromArray($jobData);
                }

                return $j;
            }
        }

        return null;
    }

    /**
     * Get the available locations for job classes. This does not include packages
     * @param bool $includeConcreteDirJobs
     * @return array
     */
    protected function getJobClassLocations($includeConcreteDirJobs = true)
    {
        if (!$includeConcreteDirJobs) {
            $jobClassLocations = [DIR_FILES_JOBS];
        } else {
            $jobClassLocations = [DIR_FILES_JOBS, DIR_FILES_JOBS_CORE];
        }

        return $jobClassLocations;
    }

    /**
     * Determine a job's proper class name
     * @param string $handle
     * @param null|string $pkgHandle
     * @return string
     */
    protected function getClassName($handle, $pkgHandle = null)
    {
        $className = camelcase($handle);
        $fqn = overrideable_core_class(
            "Job\\{$className}",
            DIRNAME_JOBS . "/{$handle}.php",
            $pkgHandle
        );

        return $fqn;
    }

}
