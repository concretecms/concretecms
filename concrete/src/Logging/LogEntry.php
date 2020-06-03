<?php

namespace Concrete\Core\Logging;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Monolog\Logger as Monolog;

class LogEntry
{
    /**
     * Gets the level of the log.
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
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
     * Gets the message of the log.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Gets the channel of the log.
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
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
     * Gets the id of the log.
     *
     * @return int
     */
    public function getID()
    {
        return $this->logID;
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
                return '<i class="text-danger fa fa-fire launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::CRITICAL:
                return '<i class="text-danger fa fa-ambulance launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::ALERT:
                return '<i class="text-danger fa fa-exclamation-circle launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::ERROR:
                return '<i class="text-danger fa fa-flag launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::WARNING:
                return '<i class="text-warning fa fa-warning launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::NOTICE:
                return '<i class="text-success fa fa-leaf launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::INFO:
                return '<i class="text-info fa fa-info-circle launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Monolog::DEBUG:
                return '<i class="text-info fa fa-cog launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
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
