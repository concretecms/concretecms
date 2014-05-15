<?
namespace Concrete\Core\Logging;

use Database;
use User;
use Core;

class LogEntry
{

    public function getLevel()
    {
        return $this->level;
    }

    public function getLevelName()
    {
        return Logger::getLevelName($this->level);
    }

    public function getLevelDisplayName()
    {
        return ucfirst(strtolower(Logger::getLevelName($this->level)));
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getChannelDisplay()
    {
        return Core::make('helper/text')->unhandle($this->channel);
    }

    public function getID()
    {
        return $this->logID;
    }

    public function getLevelIcon()
    {
        switch($this->getLevel()) {
            case Logger::EMERGENCY:
                return '<i class="text-danger glyphicon glyphicon-fire launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Logger::CRITICAL:
            case Logger::ALERT:
                return '<i class="text-danger glyphicon glyphicon-exclamation-sign launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Logger::ERROR:
            case Logger::WARNING:
                return '<i class="text-warning glyphicon glyphicon-warning-sign launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Logger::INFO:
                return '<i class="text-info glyphicon glyphicon-info-sign launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
            case Logger::DEBUG:
                return '<i class="text-info glyphicon glyphicon-cog launch-tooltip" title="' . $this->getLevelDisplayName() . '"></i>';
        }
    }

    public function getUserID()
    {
        return $this->uID;
    }

    public function getUserObject()
    {
        if ($this->getUserID()) {
            $u = User::getByUserID($this->getUserID());
            if (is_object($u)) {
                return $u;
            }
        }
    }

    public function getTimestamp()
    {
        return $this->time;
    }

    public static function getByID($logID)
    {
        $db = Database::get();
        $r = $db->Execute("select * from Logs where logID = ?", array($logID));
        if ($r) {
            $row = $r->FetchRow();
            $obj = new static();
            $obj = array_to_object($obj, $row);
            return $obj;
        }
    }

}
