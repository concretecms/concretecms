<?php
namespace Concrete\Core\Job;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Package\PackageList;
use Core;
use Database;
use Events;
use Exception;

abstract class Job extends Object
{
    const JOB_SUCCESS = 0;
    const JOB_ERROR_EXCEPTION_GENERAL = 1;

    const JOB_STATUS_ENABLED = 'ENABLED';
    const JOB_STATUS_RUNNING = 'RUNNING';
    const JOB_STATUS_ERROR = 'ERROR';
    const JOB_STATUS_DISABLED_ERROR = 'DISABLED_ERROR';
    const JOB_STATUS_DISABLED = 'DISABLED';

    protected $pkgID;
    protected $jLastStatusCode;
    protected $jLastStatusText;

    abstract public function run();
    abstract public function getJobName();
    abstract public function getJobDescription();

    /** @var Application */
    protected static $app;

    /**
     * DO NOT USE THIS METHOD
     * Instead override the application bindings.
     * This method only exists to enable legacy static methods on the real application instance.
     *
     * @deprecated Create a job using $app->make('job');
     *
     * @param Application $app
     */
    public static function setApplicationObject(Application $app)
    {
        static::$app = $app;
    }

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
     * @return bool
     */
    public function didFail()
    {
        return in_array($this->jLastStatusCode, [
            static::JOB_ERROR_EXCEPTION_GENERAL,
        ]);
    }

    /**
     * @return bool
     */
    public function canUninstall()
    {
        return $this->jNotUninstallable != 1;
    }

    /**
     * @return bool
     */
    public function supportsQueue()
    {
        return $this instanceof QueueableJob;
    }

    //==========================================================
    // JOB MANAGEMENT - do not override anything below this line
    //==========================================================

    //Other Job Variables
    public $availableJStatus = [
        self::JOB_STATUS_ENABLED,
        self::JOB_STATUS_RUNNING,
        self::JOB_STATUS_ERROR,
        self::JOB_STATUS_DISABLED_ERROR,
        self::JOB_STATUS_DISABLED,
    ];
    public $jID = 0;
    public $jStatus = self::JOB_STATUS_ENABLED;
    public $jDateLastRun;
    public $jHandle = '';
    public $jNotUninstallable = 0;
    public $isScheduled = 0;
    public $scheduledInterval = 'days'; // hours|days|weeks|months
    public $scheduledValue = 0;

    /**
     * @return string DateTime
     */
    public function getDateLastRun()
    {
        return $this->jDateLastRun;
    }

    /**
     * @param string $datetime
     *
     * @return Job $this
     */
    public function setDateLastRun($datetime)
    {
        $this->jDateLastRun = $datetime;

        return $this;
    }

    /**
     * E.g. 'RUNNING'.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->jStatus;
    }

    public function getLastStatusCode()
    {
        return $this->jLastStatusCode;
    }

    /**
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
     *
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
     * @return Job $this
     */
    public function reset()
    {
        $this->jStatus = self::JOB_STATUS_ENABLED;

        $db = static::$app['database'];
        $db->executeQuery("UPDATE Jobs SET jLastStatusCode = 0, jStatus = ? WHERE jID = ?", [
            $this->jStatus,
            $this->jID,
        ]);

        return $this;
    }

    /**
     * @return Job $this
     */
    public function markStarted()
    {
        $je = new Event($this);

        static::$app['director']->dispatch('on_before_job_execute', $je);

        $timestampH = date('Y-m-d g:i:s A');
        $this->jDateLastRun = $timestampH;
        $this->jStatus = self::JOB_STATUS_RUNNING;

        $db = static::$app['database'];
        $db->executeQuery("UPDATE Jobs SET jStatus=?, jDateLastRun=NOW() WHERE jHandle=?", [
            $this->jStatus,
            $this->jHandle,
        ]);

        return $this;
    }

    /**
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

        $db = static::$app['database'];
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
        $obj->jDateLastRun = static::$app->make('helper/date')->formatDateTime('now', true, true);
        $obj->jHandle = $this->getJobHandle();
        $obj->jID = $this->getJobID();

        $this->jLastStatusCode = $resultCode;
        $this->jLastStatusText = $resultMsg;
        $this->jStatus = $jStatus;

        return $obj;
    }

    /**
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
     * @param string $jStatus
     *
     * @return Job $this
     */
    public function setJobStatus($jStatus = self::JOB_STATUS_ENABLED)
    {
        if (!in_array($jStatus, $this->availableJStatus)) {
            $jStatus = self::JOB_STATUS_ENABLED;
        }

        $this->jStatus = $jStatus;

        $db = static::$app['database'];
        $db->executeQuery("UPDATE Jobs SET jStatus=? WHERE jHandle=?", [
            $this->jStatus,
            $this->jHandle,
        ]);

        return $this;
    }

    /**
     * @return Job $this
     */
    public function install()
    {
        $db = static::$app['database'];
        $jobExists = $db->fetchColumn("SELECT count(1) FROM Jobs WHERE jHandle=?", [
            $this->jHandle,
        ]);

        $values = [
            $this->getJobName(),
            $this->getJobDescription(),
            $this->jNotUninstallable,
            $this->jHandle,
        ];

        if ($jobExists) {
            $db->executeQuery("INSERT INTO Jobs SET jName=?, jDescription=?, jDateInstalled=NOW(), jNotUninstallable=? WHERE jHandle=?", $values);
        } else {
            $db->executeQuery("INSERT INTO Jobs (jName, jDescription, jDateInstalled, jNotUninstallable, jHandle) VALUES(?,?,NOW(),?,?)", $values);
        }

        $je = new Event($this);
        Events::dispatch('on_job_install', $je);

        $this->jID = $db->lastInsertId();

        return $this;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $je = new Event($this);
        $je = Events::dispatch('on_job_uninstall', $je);
        if (!$je) {
            return false;
        }

        $db = static::$app['database'];
        $db->query("DELETE FROM Jobs WHERE jHandle=?", [
            $this->jHandle,
        ]);

        return true;
    }

    /**
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
     * @return bool
     */
    public function isScheduled()
    {
        return (bool) $this->isScheduled;
    }

    /**
     * E.g. "weeks" or "minutes".
     *
     * @return string
     */
    public function getScheduledInterval()
    {
        return $this->scheduledInterval;
    }

    /**
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
        $this->scheduledInterval = static::$app->make('helper/security')->sanitizeString($interval);
        $this->scheduledValue = intval($value);

        $db = static::$app['database'];
        $db->query("UPDATE Jobs SET isScheduled = ?, scheduledInterval = ?, scheduledValue = ? WHERE jID = ?", [
            $this->isScheduled,
            $this->scheduledInterval,
            $this->scheduledValue,
            $this->getJobID(),
        ]);

        return $this;
    }

    /**
     * @param bool $scheduled
     *
     * @return Job $this
     */
    public function setIsScheduled($scheduled = true)
    {
        $this->isScheduled = $scheduled ? 1 : 0;

        $db = static::$app['database'];
        $db->query("UPDATE Jobs SET isScheduled = ? WHERE jID = ?", [
            $this->isScheduled,
            $this->getJobID(),
        ]);

        return $this;
    }

    /**
     * @deprecated
     *
     * @param bool $scheduledOnly
     *
     * @return Job[]
     */
    public static function getList($scheduledOnly = false)
    {
        return self::$app->make(JobFactory::class)->getList($scheduledOnly);
    }

    /**
     * @deprecated
     *
     * @param int $jID
     *
     * @return null|Job
     */
    public static function getByID($jID = 0)
    {
        return self::$app->make(JobFactory::class)->getByID($jID);
    }

    /**
     * @deprecated
     *
     * @param string $jHandle
     *
     * @return null|Job
     */
    public static function getByHandle($jHandle = '')
    {
        return self::$app->make(JobFactory::class)->getByHandle($jHandle);
    }

    /**
     * @deprecated
     *
     * @param string $jHandle
     * @param array $jobData
     *
     * @return null|Job
     */
    public static function getJobObjByHandle($jHandle = '', $jobData = [])
    {
        return self::$app->make(JobFactory::class)->getJobObjByHandle($jHandle, $jobData);
    }

    /**
     * @deprecated
     *
     * Scan job directories for job classes.
     *
     * @param bool $includeConcreteDirJobs
     *
     * @return array
     */
    public static function getAvailableList($includeConcreteDirJobs = true)
    {
        return self::$app->make(JobFactory::class)->getNotInstalledJobs($includeConcreteDirJobs);
    }

    /**
     * @deprecated
     *
     * @param $pkg
     *
     * @return array
     */
    public static function getListByPackage($pkg)
    {
        return self::$app->make(JobFactory::class)->getListByPackage($pkg);
    }

    /**
     * @deprecated
     *
     * @param $jHandle
     * @param $pkg
     *
     * @return null|Job
     */
    public static function installByPackage($jHandle, $pkg)
    {
        return self::$app->make(Service::class)->installByPackage($jHandle, $pkg);
    }

    /**
     * @deprecated
     *
     * @param string $jHandle
     */
    public static function installByHandle($jHandle = '')
    {
        return self::$app->make(Service::class)->installByHandle($jHandle);
    }

    /**
     * @deprecated
     *
     * Removes Job log entries.
     */
    public static function clearLog()
    {
        return self::$app->make(Service::class)->clearLog();
    }

    /**
     * @deprecated
     *
     * @return string
     */
    public static function generateAuth()
    {
        return self::$app->make(Service::class)->generateAuth();
    }

    /**
     * @deprecated
     *
     * @param $xml
     */
    public static function exportList($xml)
    {
        return self::$app->make(Service::class)->exportList($xml);
    }

    /**
     * @deprecated
     *
     * This shouldn't be coupled with the Job class.
     *
     * @return bool|mixed
     */
    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    /**
     * @deprecated
     *
     * Use getDateLastRun()
     *
     * @return string DateTime
     */
    public function getJobDateLastRun()
    {
        return $this->getDateLastRun();
    }

    /**
     * @deprecated
     *
     * @return mixed
     */
    public function getJobLastStatusCode()
    {
        return $this->getLastStatusCode();
    }

    /**
     * @deprecated
     *
     * @return string
     */
    public function getJobLastStatusText()
    {
        return $this->getLastStatusText();
    }

    /**
     * @deprecated
     *
     * @return string
     */
    public function getJobStatus()
    {
        return $this->getStatus();
    }
}
