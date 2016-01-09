<?php
namespace Concrete\Core\User;
use \Concrete\Core\Foundation\Object;
use Loader;
use UserInfo as ConcreteUserInfo;
class Statistics extends Object {

	protected $ui;

	public function __construct($ui) {
		$this->ui = $ui;
	}


	// The logic on this is a little weird. We're trying to show the number
	// of visits since last login. So we're taking your last login, and your
	// previous login (the one before that) and finding the amount of visits
	// between those values.

	public function getPreviousSessionPageViews() {
		$db = Loader::db();
		$ui = $this->ui;
		$v = array($ui->getUserID(), $ui->getPreviousLogin(), $ui->getLastLogin());
		$num = $db->getOne("select count(pstID) from PageStatistics where uID <> ? and PageStatistics.timestamp between FROM_UNIXTIME(?) and FROM_UNIXTIME(?)", $v);
		return $num;
	}

	public static function getTotalRegistrationsForDay($date) {
		$db = Loader::db();
		$num = $db->GetOne('select count(uID) from Users where uDateAdded >= ? and uDateAdded <= ?', array($date . ' 00:00:00', $date . ' 23:59:59'));
		return $num;
	}

	public static function getLastLoggedInUser() {
		$db = Loader::db();
		$uID = $db->GetOne("select uID from Users order by uLastLogin desc");
		return ConcreteUserInfo::getByID($uID);
	}

}
