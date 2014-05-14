<?
namespace Concrete\Core\Logging;

use Database;
use User;

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

    public function getMessage()
    {
        return $this->message;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function getID()
    {
        return $this->logID;
    }

    public function getUserID()
    {
        return $this->logUserID;
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
