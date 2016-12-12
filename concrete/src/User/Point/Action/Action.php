<?php
namespace Concrete\Core\User\Point\Action;

use Database;
use Environment;
use Concrete\Core\Package\PackageList;
use Group;
use Core;
use User;
use UserInfo;
use Concrete\Core\User\Point\Entry as UserPointEntry;

class Action
{
    public $upaID;
    public $upaHandle;
    public $upaName;
    public $upaDefaultPoints;
    public $gBadgeID;

    public function load($upaID)
    {
        $db = Database::connection();
        $row = $db->GetRow('select * from UserPointActions where upaID = ?', array($upaID));
        $this->setDataFromArray($row);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->delete('UserPointActions', array('upaID' => $this->upaID));
    }

    /**
     * @param $upaID
     *
     * @return UserPointAction
     */
    public static function getByID($upaID)
    {
        $db = Database::connection();
        $row = $db->getRow("SELECT * FROM UserPointActions WHERE upaID = ?", array($upaID));
        if ($row['upaID']) {
            $upa = static::getClass($row);
            $upa->setDataFromArray($row);

            return $upa;
        }
    }

    protected static function getClass($row)
    {
        $standardClass = '\\Concrete\Core\\User\\Point\\Action\\Action';
        if ($row['upaHasCustomClass']) {
            $handleClass = Core::make('helper/text')->camelcase($row['upaHandle']) . 'Action';
            $pkgHandle = PackageList::getHandle($row['pkgID']);
            $customClass = overrideable_core_class(
                'Core\\User\\Point\\Action\\' . $handleClass,
                DIRNAME_CLASSES . '/User/Point/Action/' . $handleClass . '.php',
                $pkgHandle
            );
            try {
                $upa = Core::make($customClass);
            } catch (\ReflectionException $e) {
                $upa = Core::make($standardClass);
            }
        } else {
            $upa = Core::make($standardClass);
        }

        return $upa;
    }

    /**
     * @param \Package $pkg
     *
     * @return array
     */
    public static function getListByPackage($pkg)
    {
        $db = Database::connection();
        $upaIDs = $db->GetCol('select upaID from UserPointActions where pkgID = ? order by upaName asc', array($pkg->getPackageID()));
        $actions = array();
        foreach ($upaIDs as $upaID) {
            $action = static::getByID($upaID);
            if (is_object($action)) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    /**
     * @param $upaHandle
     *
     * @return UserPointAction
     */
    public static function getByHandle($upaHandle)
    {
        $db = Database::connection();
        $row = $db->getRow("SELECT * FROM UserPointActions WHERE upaHandle = ?", array($upaHandle));
        if ($row['upaID']) {
            $upa = static::getClass($row);
            $upa->setDataFromArray($row);

            return $upa;
        }
    }

    public static function add($upaHandle, $upaName, $upaDefaultPoints, $group, $upaIsActive = true, $pkg = false)
    {
        $upa = new static();
        $upa->upaHandle = $upaHandle;
        $upa->upaName = $upaName;
        $upa->upaDefaultPoints = $upaDefaultPoints;
        $upa->gBadgeID = 0;
        $upa->upaHasCustomClass = 0;
        $upa->upaIsActive = $upaIsActive;
        if (!$upaIsActive) {
            $upa->upaIsActive = 0;
        }

        if (is_object($group)) {
            $upa->gBadgeID = $group->getGroupID();
        }
        $upa->pkgID = 0;
        $pkgHandle = false;
        if (is_object($pkg)) {
            $upa->pkgID = $pkg->getPackageID();
            $pkgHandle = $pkg->getPackageHandle();
        }

        $env = Environment::get();
        $upaHandleCamel = \Core::make("helper/text")->camelcase($upaHandle);
        $r = $env->getRecord(DIRNAME_CLASSES . '/User/Point/Action/' . $upaHandleCamel . 'Action.php', $pkgHandle);
        if ($r->exists()) {
            $upa->upaHasCustomClass = 1;
        }
        $upa->save();
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    protected function setDataFromArray($data)
    {
        if (is_array($data) && count($data)) {
            $this->upaID = $data['upaID'];
            $this->upaHandle = $data['upaHandle'];
            $this->upaName = $data['upaName'];
            $this->upaDefaultPoints = $data['upaDefaultPoints'];
            $this->upaHasCustomClass = $data['upaHasCustomClass'];
            $this->gBadgeID = $data['gBadgeID'];
            $this->upaIsActive = $data['upaIsActive'];

            return true;
        } else {
            return false;
        }
    }

    public function getAttributeNames()
    {
        return array('upaID', 'upaHandle', 'upaName', 'upaDefaultPoints', 'gBadgeID', 'upaIsActive');
    }

    /**
     * @return bool
     */
    public function hasCustomClass()
    {
        return $this->upaHasCustomClass ? true : false;
    }

    public function getPackageHandle()
    {
        return PackageList::getHandle($this->pkgID);
    }

    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * @return string
     */
    public function getUserPointActionHandle()
    {
        return $this->upaHandle;
    }

    /**
     * @return string
     */
    public function getUserPointActionName()
    {
        return $this->upaName;
    }

    /**
     * @return int
     */
    public function getUserPointActionID()
    {
        return $this->upaID;
    }

    /**
     * @return int
     */
    public function getUserPointActionDefaultPoints()
    {
        return $this->upaDefaultPoints;
    }

    /**
     * @return int
     */
    public function getUserPointActionBadgeGroupID()
    {
        return $this->gBadgeID;
    }

    public function isUserPointActionActive()
    {
        return $this->upaIsActive;
    }

    /**
     * @return Group
     */
    public function getUserPointActionBadgeGroupObject()
    {
        return Group::getByID($this->getUserPointActionBadgeGroupID());
    }

    public function addEntry($user, ActionDescription $descr, $points = false, $date = null)
    {
        if (!$this->isUserPointActionActive()) {
            return false;
        }

        if (is_object($user)) {
            $user = UserInfo::getByID($user->getUserID());
            $uID = $user->getUserID();
        } else {
            $uID = $user;
        }

        if (!isset($uID) || $uID <= 0) {
            return false;
        }

        $g = $this->getUserPointActionBadgeGroupObject();
        if ($g instanceof Group) {
            if ($user instanceof \Concrete\Core\User\UserInfo) {
                $user = User::getByUserID($user->getUserID());
            }
            $user->enterGroup($g);
        }

        if ($date == null) {
            $date = date('Y-m-d H:i:s');
        }

        if ($points === false) {
            $points = $this->getUserPointActionDefaultPoints();
        }

        try {
            $upe = new UserPointEntry();
            $upe->upuID = $uID;
            $upe->upaID = $this->upaID;
            $upe->upPoints = $points;
            $upe->timestamp = $date;
            $descr = serialize($descr);
            $upe->object = $descr;
            $upe->save();

            return $upe;
        } catch (Exception $e) {
            Log::addEntry(t("Error saving user point record: %s", $e->getMessage()), 'exceptions');

            return false;
        }

        return true;
    }

    public function save()
    {
        $db = Database::connection();
        if ($this->upaID) {
            $db->update('UserPointActions', array(
                'upaHandle' => $this->upaHandle,
                'upaName' => $this->upaName,
                'upaDefaultPoints' => $this->upaDefaultPoints,
                'upaHasCustomClass' => $this->upaHasCustomClass,
                'upaIsActive' => $this->upaIsActive,
                'gBadgeID' => $this->gBadgeID,
            ), array("upaID" => $this->upaID));
        } else {
            $res = $db->insert('UserPointActions', array(
                'upaHandle' => $this->upaHandle,
                'upaName' => $this->upaName,
                'upaDefaultPoints' => $this->upaDefaultPoints,
                'upaHasCustomClass' => $this->upaHasCustomClass,
                'upaIsActive' => $this->upaIsActive,
                'gBadgeID' => $this->gBadgeID,
            ));
        }
    }
}
