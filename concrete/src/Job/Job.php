<?php
namespace Concrete\Core\Job;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;
use Core;
use Database;
use Events;
use Exception;

/**
 * Abstract Job class
 * This class can be extended to create schedulable tasks in concrete5.
 * Typically a job only needs to define `getJobName,
 *
 * @package Concrete\Core\Job
 */
abstract class Job extends Object
{

    // Job results
    const JOB_SUCCESS = 0;
    const JOB_ERROR_EXCEPTION_GENERAL = 1;

    // Job Statuses
    const JOB_STATUS_ENABLED = 'ENABLED';
    const JOB_STATUS_RUNNING = 'RUNNING';
    const JOB_STATUS_ERROR = 'ERROR';
    const JOB_STATUS_DISABLED_ERROR = 'DISABLED_ERROR';
    const JOB_STATUS_DISABLED = 'DISABLED';

    /** @var int|null The package ID that installed this job */
    protected $pkgID;

    /** @var int The last status code */
    protected $jLastStatusCode;

    /** @var string The last status string */
    protected $jLastStatusText;

    /**
     * @var string[] Available statuses
     */
    public $availableJStatus = [
        self::JOB_STATUS_ENABLED,
        self::JOB_STATUS_RUNNING,
        self::JOB_STATUS_ERROR,
        self::JOB_STATUS_DISABLED_ERROR,
        self::JOB_STATUS_DISABLED,
    ];

    /** @var int Job ID */
    public $jID = 0;

    /** @var string Job Status, see $job->availableJStatus for statuses */
    public $jStatus = self::JOB_STATUS_ENABLED;

    /** @var string A date string */
    public $jDateLastRun;

    /** @var string */
    public $jHandle = '';

    /** @var bool */
    public $jNotUninstallable = false;

    /** @var bool */
    public $isScheduled = false;

    /** @var string hours|days|weeks|months */
    public $scheduledInterval = 'days';

    /** @var int */
    public $scheduledValue = 0;

    /** @var Application */
    private static $staticApp;

    /**
     * Run this job's task.
     * Use this method to provide functionality to your job
     * @return void
     */
    abstract public function run();

    /**
     * Get the current job name
     * @return string
     */
    abstract public function getJobName();

    /**
     * Get this job's description
     * @return string
     */
    abstract public function getJobDescription();

    /**
     * @return string
     */
    public function getJobHandle()
    {
        return $this->jHandle;
    }

    /**
     * @return int
     */
    public function getJobID()
    {
        return $this->jID;
    }

    /**
     * Did this job fail the last time it ran
     * @return bool
     */
    public function didFail()
    {
        return in_array($this->jLastStatusCode, [
            static::JOB_ERROR_EXCEPTION_GENERAL,
        ]);
    }

    /**
     * Is this job uninstallable
     * @return bool
     */
    public function canUninstall()
    {
        return $this->jNotUninstallable != 1;
    }

    /**
     * Does this job support queuing
     * We just return whether or not this job is an instance of QueueableJob
     * @return bool
     */
    public function supportsQueue()
    {
        return $this instanceof QueueableJob;
    }

    /**
     * Get the last date that this job ran
     * @return string DateTime
     */
    public function getDateLastRun()
    {
        return $this->jDateLastRun;
    }

    /**
     * Set the last date that this job ran
     * @param string $datetime
     * @return self $this
     */
    public function setDateLastRun($datetime)
    {
        $this->jDateLastRun = $datetime;

        return $this;
    }

    /**
     * Get the current status
     * @return string
     */
    public function getStatus()
    {
        return $this->jStatus;
    }

    /**
     * Get the last status code returned
     * @return int
     */
    public function getLastStatusCode()
    {
        return $this->jLastStatusCode;
    }

    /**
     * Get the last status text returned
     * @return string
     */
    public function getLastStatusText()
    {
        return $this->jLastStatusText;
    }

    /**
     * authenticateRequest checks against your site's job security token and a custom auth field to make
     * sure that this is a request that is coming either from something cronned by the site owner
     * or from the dashboard.
     *
     * @param string $auth
     * @return bool
     */
    public static function authenticateRequest($auth)
    {
        $auth_from_config = Core::make(Service::class)->generateAuth();

        if ($auth_from_config !== $auth) {
            return false;
        }

        return true;
    }

    /**
     * Reset this job so that it can run again
     * @return self
     */
    public function reset()
    {
        $this->jStatus = self::JOB_STATUS_ENABLED;

        $db = static::$staticApp['database'];
        $db->executeQuery("UPDATE Jobs SET jLastStatusCode = 0, jStatus = ? WHERE jID = ?", [
            $this->jStatus,
            $this->jID,
        ]);

        return $this;
    }

    /**
     * Mark this job as having started
     * @return self
     */
    public function markStarted()
    {
        $je = new Event($this);

        static::$staticApp['director']->dispatch('on_before_job_execute', $je);

        $timestampH = date('Y-m-d g:i:s A');
        $this->jDateLastRun = $timestampH;
        $this->jStatus = self::JOB_STATUS_RUNNING;

        $db = static::$staticApp['database'];
        $db->executeQuery("UPDATE Jobs SET jStatus=?, jDateLastRun=NOW() WHERE jHandle=?", [
            $this->jStatus,
            $this->jHandle,
        ]);

        return $this;
    }

    /**
     * Mark this job as having completed
     * @param int $resultCode
     * @param bool|string $resultMsg
     *
     * @return JobResult
     */
    public function markCompleted($resultCode = 0, $resultMsg = false)
    {
        $resultMsg = $resultMsg ?: t('The Job was run successfully.');
        $resultCode = $resultCode ?: 0;
        $jStatus = $this->didFail() ? self::JOB_STATUS_ERROR : self::JOB_STATUS_ENABLED;
        $timestamp = date('Y-m-d H:i:s');

        $db = static::$staticApp['database'];
        $db->executeQuery("UPDATE Jobs SET jStatus=?, jLastStatusCode = ?, jLastStatusText=? WHERE jHandle=?", [
            $jStatus,
            $resultCode,
            $resultMsg,
            $this->jHandle,
        ]);

        $db->executeQuery("INSERT INTO JobsLog (jID, jlMessage, jlTimestamp, jlError) VALUES(?,?,?,?)", [
            $this->jID,
            $resultMsg,
            $timestamp,
            $resultCode,
        ]);

        $je = new Event($this);
        Events::dispatch('on_job_execute', $je);

        $obj = new JobResult();
        $obj->error = $resultCode;
        $obj->result = $resultMsg;
        $obj->jDateLastRun = static::$staticApp->make('helper/date')->formatDateTime('now', true, true);
        $obj->jHandle = $this->getJobHandle();
        $obj->jID = $this->getJobID();

        $this->jLastStatusCode = $resultCode;
        $this->jLastStatusText = $resultMsg;
        $this->jStatus = $jStatus;

        return $obj;
    }

    /**
     * Run the job
     * @return JobResult
     */
    public function executeJob()
    {
        $error = '';
        $this->markStarted();

        try {
            $resultMsg = $this->run();

            if (strlen($resultMsg) === 0) {
                $resultMsg = t('The Job was run successfully.');
            }
        } catch (Exception $e) {
            $resultMsg = $e->getMessage();
            $error = static::JOB_ERROR_EXCEPTION_GENERAL;
        }

        $obj = $this->markCompleted($error, $resultMsg);

        return $obj;
    }

    /**
     * Set the current status
     * @param string $jStatus
     * @return self
     */
    public function setJobStatus($jStatus = self::JOB_STATUS_ENABLED)
    {
        if (!in_array($jStatus, $this->availableJStatus)) {
            $jStatus = self::JOB_STATUS_ENABLED;
        }

        $this->jStatus = $jStatus;

        $db = static::$staticApp['database'];
        $db->executeQuery("UPDATE Jobs SET jStatus=? WHERE jHandle=?", [
            $this->jStatus,
            $this->jHandle,
        ]);

        return $this;
    }

    /**
     * Check if this job is scheduled and should run now
     * @return bool
     */
    public function isScheduledForNow()
    {
        if (!$this->isScheduled()) {
            return false;
        }

        if ($this->getScheduledValue() <= 0) {
            return false;
        }

        $last_run = strtotime($this->jDateLastRun);
        $seconds = 1;
        switch ($this->getScheduledInterval()) {
            case "minutes":
                $seconds = 60;
                break;
            case "hours":
                $seconds = 60 * 60;
                break;
            case "days":
                $seconds = 60 * 60 * 24;
                break;
            case "weeks":
                $seconds = 60 * 60 * 24 * 7;
                break;
            case "months":
                $seconds = 60 * 60 * 24 * 7 * 30;
                break;
        }

        $gap = $this->getScheduledValue() * $seconds;
        if ($last_run > (time() - $gap)) {
            return false;
        }

        return true;
    }

    /**
     * Check if this job is scheduled
     * @return bool
     */
    public function isScheduled()
    {
        return (bool) $this->isScheduled;
    }

    /**
     * Get the scheduled interval for this job
     * E.g. "weeks" or "minutes".
     *
     * @return string
     */
    public function getScheduledInterval()
    {
        return $this->scheduledInterval;
    }

    /**
     * Get the scheduled value
     * @return int
     */
    public function getScheduledValue()
    {
        return (int) $this->scheduledValue;
    }

    /**
     * Examples:
     * - setSchedule(1, "days", 7);
     * - setSchedule(1, "minutes", 45);.
     *
     * @param bool $scheduled
     * @param string $interval
     * @param int $value
     *
     * @return Job|false
     */
    public function setSchedule($scheduled, $interval, $value)
    {
        if (!$this->getJobID()) {
            return false;
        }

        $this->isScheduled = $scheduled ? 1 : 0;
        $this->scheduledInterval = static::$staticApp->make('helper/security')->sanitizeString($interval);
        $this->scheduledValue = intval($value);

        $db = static::$staticApp['database'];
        $db->query("UPDATE Jobs SET isScheduled = ?, scheduledInterval = ?, scheduledValue = ? WHERE jID = ?", [
            $this->isScheduled,
            $this->scheduledInterval,
            $this->scheduledValue,
            $this->getJobID(),
        ]);

        return $this;
    }

    /**
     * Set the scheduled status of this job
     * @param bool $scheduled Is this job scheduled
     * @return Job $this
     */
    public function setIsScheduled($scheduled = true)
    {
        $this->isScheduled = $scheduled ? 1 : 0;

        $db = static::$staticApp['database'];
        $db->query("UPDATE Jobs SET isScheduled = ? WHERE jID = ?", [
            $this->isScheduled,
            $this->getJobID(),
        ]);

        return $this;
    }

    /**
     * Get the package ID associated with this job.
     * @return int|null
     */
    public function getPackageID()
    {
        return $this->pkgID ? (int) $this->pkgID : null;
    }

    /**
     * @param int $pkgID
     * @return $this
     */
    public function setPackageID($pkgID)
    {
        $this->pkgID = $pkgID;

        return $this;
    }


    /**
     * Deprecated Legacy Methods
     */

    /**
     * DO NOT USE THIS METHOD
     *
     * @deprecated
     *
     * Instead override the application bindings.
     * This method only exists to enable legacy static methods on the real application instance.
     *
     * @param Application $app
     */
    public static function setStaticApplicationObject(Application $app)
    {
        static::$staticApp = $app;
    }

    /**
     * @return Job $this
     * @deprecated Use \Concrete\Core\Job\Service to manage jobs
     */
    public function install()
    {
        return self::$staticApp->make(Service::class)->install($this->jHandle);
    }

    /**
     * @return bool
     * @deprecated Use \Concrete\Core\Job\Service to manage jobs
     */
    public function uninstall()
    {
        return self::$staticApp->make(Service::class)->uninstall($this->jHandle);
    }

    /**
     * @param bool $scheduledOnly
     * @return Job[]
     *
     * @deprecated Use \Concrete\Core\Job\JobFactory to get job objects
     */
    public static function getList($scheduledOnly = false)
    {
        $factory = self::$staticApp->make(JobFactory::class);

        if ($scheduledOnly) {
            return $factory->scheduled();
        }

        return $factory->installed();
    }

    /**
     * @param int $jID
     * @return null|Job
     *
     * @deprecated Use \Concrete\Core\Job\JobFactory to get job objects
     */
    public static function getByID($jID = 0)
    {
        return self::$staticApp->make(JobFactory::class)->getByID($jID);
    }

    /**
     * @param string $jHandle
     * @return null|Job
     *
     * @deprecated Use \Concrete\Core\Job\JobFactory to get job objects
     */
    public static function getByHandle($jHandle = '')
    {
        return self::$staticApp->make(JobFactory::class)->getByHandle($jHandle);
    }

    /**
     * @param string $jHandle
     * @param array $jobData
     * @return null|Job
     *
     * @deprecated Use \Concrete\Core\Job\JobFactory to get job objects
     */
    public static function getJobObjByHandle($jHandle = '', $jobData = [])
    {
        return self::$staticApp->make(JobFactory::class)->getJobByHandle($jHandle, $jobData);
    }

    /**
     * @param bool $includeConcreteDirJobs
     * @return array
     *
     * @deprecated See $JobFactory->getNotInstalledJobs();
     */
    public static function getAvailableList($includeConcreteDirJobs = true)
    {
        return self::$staticApp->make(JobFactory::class)->getNotInstalledJobs($includeConcreteDirJobs);
    }

    /**
     * @param $pkg
     * @return array
     *
     * @deprecated See $JobFactory->installed($package);
     */
    public static function getListByPackage($pkg)
    {
        return self::$staticApp->make(JobFactory::class)->installed($pkg);
    }

    /**
     * @param $jHandle
     * @param $pkg
     * @return null|Job
     *
     * @deprecated Use \Concrete\Core\Job\Service to manage jobs
     */
    public static function installByPackage($jHandle, $pkg)
    {
        return self::$staticApp->make(Service::class)->install($jHandle, $pkg);
    }

    /**
     * @param string $jHandle
     * @return Job
     * @deprecated Use \Concrete\Core\Job\Service to manage jobs
     */
    public static function installByHandle($jHandle = '')
    {
        return self::$staticApp->make(Service::class)->install($jHandle);
    }

    /**
     * @deprecated Use \Concrete\Core\Job\Service to manage jobs
     */
    public static function clearLog()
    {
        return self::$staticApp->make(Service::class)->clearLog();
    }

    /**
     * @return string
     * @deprecated Use \Concrete\Core\Job\Service to manage jobs
     */
    public static function generateAuth()
    {
        return self::$staticApp->make(Service::class)->generateAuth();
    }

    /**
     * @param $xml
     *
     * @deprecated Use \Concrete\Core\Job\Service to manage jobs
     */
    public static function exportList($xml)
    {
        return self::$staticApp->make(Service::class)->exportList($xml);
    }

    /**
     * @return bool|mixed
     *
     * @deprecated This job likely doesn't know the handle. Instead use $job->getPackageID() and determine the handle manually
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * @deprecated Use is $job->getDateLastRun();
     */
    public function getJobDateLastRun()
    {
        return $this->getDateLastRun();
    }

    /**
     * @deprecated Use is $job->getLastStatusCode();
     */
    public function getJobLastStatusCode()
    {
        return $this->getLastStatusCode();
    }

    /**
     * @deprecated Use $job->getLastStatusText();
     */
    public function getJobLastStatusText()
    {
        return $this->getLastStatusText();
    }

    /**
     * @deprecated Use $job->getStatus();
     */
    public function getJobStatus()
    {
        return $this->getStatus();
    }
}
