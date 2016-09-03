<?php
namespace Concrete\Core\Job;

use Concrete\Core\Application\Application;
use Concrete\Core\Package\PackageList;

class JobFactory
{
    /** @var \Concrete\Core\Application\Application */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Return list of installed Jobs.
     *
     * @return Job[]
     */
    public function installed()
    {
        $jobs = [];

        $db = $this->app['database']->connection();
        $q = "SELECT jID FROM Jobs ORDER BY jDateLastRun, jID";
        $r = $db->query($q);

        while ($row = $r->fetchRow($q)) {
            $j = $this->getByID($row['jID']);
            if (is_object($j)) {
                $jobs[] = $j;
            }
        }

        return $jobs;
    }

    /**
     * Return list of scheduled Jobs.
     *
     * @return Job[]
     */
    public function scheduled()
    {
        $jobs = [];

        $db = $this->app['database']->connection();
        $q = "SELECT jID FROM Jobs WHERE isScheduled = 1 ORDER BY jDateLastRun, jID";
        $r = $db->query($q);

        while ($row = $r->fetchRow($q)) {
            $j = $this->getByID($row['jID']);
            if (is_object($j)) {
                $jobs[] = $j;
            }
        }

        return $jobs;
    }

    /**
     * @param int $jID
     *
     * @return null|Job
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

        return $this->getJobObjByHandle($jobData['jHandle'], $jobData);
    }

    /**
     * @param string $jHandle
     *
     * @return null|Job
     */
    public function getByHandle($jHandle)
    {
        $db = $this->app['database']->connection();
        $jobData = $db->fetchAssoc("SELECT * FROM Jobs WHERE jHandle=?", [
            $jHandle,
        ]);

        if (!$jobData || !$jobData['jHandle']) {
            return null;
        }

        return $this->getJobObjByHandle($jobData['jHandle'], $jobData);
    }

    /**
     * Scan job directories for job classes.
     *
     * @param bool $includeConcreteDirJobs
     *
     * @return array
     */
    public function getNotInstalledJobs($includeConcreteDirJobs = true)
    {
        $jobObjects = [];

        // Get existing jobs
        $existingJobHandles = [];
        $existingJobs = $this->getList();
        foreach ($existingJobs as $j) {
            $existingJobHandles[] = $j->getJobHandle();
        }

        $jobClassLocations = $this->getJobClassLocations($includeConcreteDirJobs);

        foreach ($jobClassLocations as $jobClassLocation) {
            // Open a known directory, and proceed to read its contents
            if (!is_dir($jobClassLocation)) {
                continue;
            }

            if ($dh = opendir($jobClassLocation)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file, strlen($file) - 4) != '.php') {
                        continue;
                    }

                    $alreadyInstalled = 0;
                    foreach ($existingJobHandles as $existingJobHandle) {
                        if (substr($file, 0, strlen($file) - 4) == $existingJobHandle) {
                            $alreadyInstalled = 1;
                            break;
                        }
                    }
                    if ($alreadyInstalled) {
                        continue;
                    }

                    $jHandle = substr($file, 0, strlen($file) - 4);
                    $className = $this->getClassName($jHandle);
                    $jobObjects[$jHandle] = $this->app->make($className);
                    $jobObjects[$jHandle]->jHandle = $jHandle;
                }
                closedir($dh);
            }
        }

        return $jobObjects;
    }

    /**
     * @param string $jHandle
     * @param array  $jobData
     *
     * @return null|Job
     */
    protected function getJobObjByHandle($jHandle = '', $jobData = [])
    {
        $jcl = $this->getJobClassLocations();
        $pkgHandle = null;

        //check for the job file in the various locations
        $db = $this->app['database']->connection();
        $pkgID = $db->fetchColumn("SELECT pkgID FROM Jobs WHERE jHandle = ?", [
            $jHandle,
        ]);

        if ($pkgID > 0) {
            $pkgHandle = PackageList::getHandle($pkgID);
            if ($pkgHandle) {
                $jcl[] = DIR_PACKAGES.'/'.$pkgHandle.'/'.DIRNAME_JOBS;
                $jcl[] = DIR_PACKAGES_CORE.'/'.$pkgHandle.'/'.DIRNAME_JOBS;
            }
        }

        foreach ($jcl as $jobClassLocation) {
            //load the file & class, then run the job
            $path = $jobClassLocation.'/'.$jHandle.'.php';
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
     * @param bool $includeConcreteDirJobs
     *
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
     * @param string      $jHandle
     * @param null|string $pkgHandle
     *
     * @return string
     */
    protected function getClassName($jHandle, $pkgHandle = null)
    {
        $class = overrideable_core_class('Job\\'.camelcase($jHandle), DIRNAME_JOBS.'/'.$jHandle.'.php', $pkgHandle);

        return $class;
    }

    /**
     * Return list of Jobs from a specific package.
     *
     * @param object $pkg
     *
     * @return Job[]
     */
    public function fromPackage($pkg)
    {
        $list = [];

        $db = $this->app['database']->connection();
        $r = $db->executeQuery("ELECT jHandle FROM Jobs WHERE pkgID = ? ORDER BY jHandle ASC", [
            $pkg->getPackageID(),
        ]);

        while ($row = $r->fetchAssoc()) {
            $list[] = $this->getJobObjByHandle($row['jHandle']);
        }

        return $list;
    }

    /**
     * @deprecated Use 'fromPackage($pkg)'
     *
     * @param $pkg
     *
     * @return array
     */
    public function getListByPackage($pkg)
    {
        return $this->fromPackage($pkg);
    }

    /**
     * @deprecated Use 'installed' and 'scheduled' methods instead.
     *
     * @param bool $scheduledOnly
     *
     * @return Job[]
     */
    public function getList($scheduledOnly = false)
    {
        if ($scheduledOnly) {
            return $this->scheduled();
        } else {
            return $this->installed();
        }
    }
}
