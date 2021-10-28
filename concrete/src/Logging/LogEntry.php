<?php

namespace Concrete\Core\Logging;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use DateTime;
use Monolog\Logger as Monolog;

class LogEntry
{
    /** @var int|null */
    public $id;
    /** @var string|null */
    public $channel;
    /** @var DateTime|null */
    public $time;
    /** @var string|null */
    public $message;
    /** @var string|null */
    public $level;
    /** @var UserInfo|null */
    public $user;

    public function __construct($row = null)
    {
        $app = Application::getFacadeApplication();
        /** @var UserInfoRepository $userInfoRepository */
        $userInfoRepository = $app->make(UserInfoRepository::class);

        if (is_array($row)) {
            if (isset($row["logID"])) {
                $this->setId($row["logID"]);
            }

            if (isset($row["channel"])) {
                $this->setChannel($row["channel"]);
            }

            if (isset($row["time"])) {
                $time = new DateTime();
                $time->setTimestamp($row["time"]);
                $this->setTime($time);
            }

            if (isset($row["message"])) {
                $this->setMessage($row["message"]);
            }

            if (isset($row["level"])) {
                $this->setLevel($row["level"]);
            }

            if (isset($row["uID"])) {
                $user = $userInfoRepository->getByID($row["uID"]);
                $this->setUser($user);
            }
        }
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return LogEntry
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string|null $channel
     * @return LogEntry
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param DateTime|null $time
     * @return LogEntry
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return LogEntry
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string|null $level
     * @return LogEntry
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return UserInfo|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInfo|null $user
     * @return LogEntry
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Gets the name of the logging level.
     *
     * @return string
     */
    public function getLevelName()
    {
        return Monolog::getLevelName($this->level);
    }

    /**
     * Gets the name of the logging level.
     *
     * @return string
     */
    public function getLevelDisplayName()
    {
        return Levels::getLevelDisplayName($this->level);
    }

    /**
     * Gets the channel name of the logging level.
     *
     * @return string
     */
    public function getChannelDisplayName()
    {
        return Channels::getChannelDisplayName($this->channel);
    }

    /**
     * Gets the HTML code for the icon of the logging level.
     *
     * @return string
     */
    public function getLevelIcon()
    {
        switch ($this->getLevel()) {
            case Monolog::EMERGENCY:
                return '<i class="text-danger fas fa-fire launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::CRITICAL:
                return '<i class="text-danger fas fa-ambulance launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::ALERT:
                return '<i class="text-danger fas fa-exclamation-circle launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::ERROR:
                return '<i class="text-danger fas fa-flag launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::WARNING:
                return '<i class="text-warning fas fa-exclamation-triangle launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::NOTICE:
                return '<i class="text-success fas fa-leaf launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::INFO:
                return '<i class="text-info fas fa-info-circle launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::DEBUG:
                return '<i class="text-info fas fa-cog launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
        }
    }

    /**
     * Gets the user id of the user that caused the log.
     *
     * @return int
     */
    public function getUserID()
    {
        return $this->uID;
    }

    /**
     * Gets the user object of the user that caused the log.
     *
     * @return \Concrete\Core\User\User|null
     */
    public function getUserObject()
    {
        if ($this->getUserID()) {
            $u = User::getByUserID($this->getUserID());
            if (is_object($u)) {
                return $u;
            }
        }
    }

    /**
     * Gets the formatted time of the log timestamp.
     *
     * @return string
     */
    public function getDisplayTimestamp()
    {
        $app = Application::getFacadeApplication();
        $dh = $app->make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

        return $dh->formatDateTime($this->time, true, true);
    }

    /**
     * Gets the timestamp of the log.
     *
     * @return string
     */
    public function getTimestamp()
    {
        return $this->time;
    }

    /**
     * Gets the log object from its id.
     *
     * @param int $logID Id of the log
     *
     * @return LogEntry|null
     */
    public static function getByID($logID)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $row = $db->fetchAssoc('select * from Logs where logID = ?', [$logID]);
        if ($row) {
            $obj = new static();
            $obj = array_to_object($obj, $row);

            return $obj;
        }
    }

    /**
     * Deletes the log entry.
     */
    public function delete()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $logID = $this->getID();
        if (!empty($logID)) {
            $db->delete('Logs', ['logID' => $logID]);
        }
    }

}