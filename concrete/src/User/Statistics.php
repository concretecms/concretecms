<?php
namespace Concrete\Core\User;

use Concrete\Core\Foundation\ConcreteObject;
use Loader;
use UserInfo as ConcreteUserInfo;

class Statistics extends ConcreteObject
{
    protected $ui;

    public function __construct($ui)
    {
        $this->ui = $ui;
    }

    public static function getTotalRegistrationsForDay($date)
    {
        $db = Loader::db();
        $num = $db->GetOne('select count(uID) from Users where uDateAdded >= ? and uDateAdded <= ?', array($date . ' 00:00:00', $date . ' 23:59:59'));

        return $num;
    }

    public static function getLastLoggedInUser()
    {
        $db = Loader::db();
        $uID = $db->GetOne("select uID from Users order by uLastLogin desc");

        return ConcreteUserInfo::getByID($uID);
    }
}
