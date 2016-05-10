<?php
namespace Concrete\Core\User\Point;

use Loader;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\Point\Action\Action as UserPointAction;

class Entry
{
    public $upID;
    public $upuID;
    public $upaID = 0;
    public $upPoints;
    public $timestamp;

    public function load($upID)
    {
        $db = Loader::db();
        $row = $db->GetRow('select * from UserPointHistory where upID = ?', array($upID));
        if (is_array($row) && $row['upID']) {
            $this->upID = $row['upID'];
            $this->upuID = $row['upuID'];
            $this->upaID = $row['upaID'];
            $this->object = $row['object'];
            $this->upPoints = $row['upPoints'];
            $this->timestamp = $row['timestamp'];
        }
    }

    public function save()
    {
        $db = Loader::db();
        if ($this->upID) {
            $db->update('UserPointHistory', array(
                'upID' => $this->upID,
                'upuID' => $this->upuID,
                'upaID' => $this->upaID,
                'object' => $this->object,
                'upPoints' => $this->upPoints,
                'timestamp' => date('Y-m-d H:i:s'),
            ), array('upID' => $this->upID));
        } else {
            $db->insert('UserPointHistory', array(
                'upID' => $this->upID,
                'upuID' => $this->upuID,
                'object' => $this->object,
                'upaID' => $this->upaID,
                'upPoints' => $this->upPoints,
                'timestamp' => date('Y-m-d H:i:s'),
            ));
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->delete('UserPointHistory', array('upID' => $this->upID));
    }

    public function getUserPointEntryID()
    {
        return $this->upID;
    }

    public function getUserPointEntryActionID()
    {
        return $this->upaID;
    }

    public function getUserPointEntryActionObject()
    {
        return UserPointAction::getByID($this->getUserPointEntryActionID());
    }

    public function getUserPointEntryValue()
    {
        return $this->upPoints;
    }

    public function getUserPointEntryTimestamp()
    {
        return $this->timestamp;
    } // - returns unix timestamp stored in the timestamp column

    public function getUserPointEntryDateTime()
    {
        return $this->timestamp;
    } // - returns it in a nicely formatted way

    public function getUserPointEntryUserID()
    {
        return $this->upuID;
    }

    public function getUserPointEntryDescriptionObject()
    {
        return unserialize($this->object);
    }

    public function getUserPointEntryDescription()
    {
        if ($this->object) {
            $obj = unserialize($this->object);

            return $obj->getUserPointActionDescription();
        }
    }

    public function getUserPointEntryUserObject()
    {
        $ui = UserInfo::getByID($this->upuID);

        return $ui;
    }

    public static function getTotal($ui)
    {
        $db = Loader::db();
        $cnt = $db->GetOne('select sum(upPoints) as total from UserPointHistory where upuID = ?',
            array($ui->getUserID()));

        return $cnt;
    }
}
