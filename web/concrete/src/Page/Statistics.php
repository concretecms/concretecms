<?php
namespace Concrete\Core\Page;
use Loader;
use Concrete\Core\Foundation\Object;
class Statistics {

	/**
	 * Gets total page views across the entire site.
	 * @param date $date
	 * @return int
	 */
	public static function getTotalPageViews($date = null) {
		$db = Loader::db();
		if ($date != null) {
			return $db->GetOne("select count(pstID) from PageStatistics where date = ?", array($date));
		}
		return $db->GetOne("select count(pstID) from PageStatistics");
	}

	/**
	 * Gets total page views for everyone but the passed user object
	 * @param User $u
	 * @param date $date
	 * @return int
	 */
	public static function getTotalPageViewsForOthers($u, $date = null) {
		$db = Loader::db();
		if ($date != null) {
			$v = array($u->getUserID(), $date);
			return $db->GetOne("select count(pstID) from PageStatistics where uID <> ? and date = ?", $v);
		}
		$v = array($u->getUserID());
		return $db->GetOne("select count(pstID) from PageStatistics where uID <> ?", $v);
	}

	/**
	 * Gets the total number of versions across all pages. Used in the dashboard.
	 * @todo It might be nice if this were a little more generalized
	 * @return int
	 */
	public static function getTotalPageVersions() {
		$db = Loader::db();
		return $db->GetOne("select count(cvID) from CollectionVersions");
	}

	/**
	 * Returns the datetime of the last edit to the site. Used in the dashboard
	 * @return string date formated like: 2009-01-01 00:00:00
	 */
	public static function getSiteLastEdit($type = 'system') {
		return Loader::db()->GetOne("select max(Collections.cDateModified) from Collections");
	}

	/**
	 * Gets the total number of pages currently in edit mode
	 * @return int
	 */
	public static function getTotalPagesCheckedOut() {
		$db = Loader::db();
		return $db->GetOne("select count(cID) from Pages where cIsCheckedOut = 1");
	}


	/**
	 * For a particular page ID, grabs all the pages of this page, and increments the cTotalChildren number for them
	 */
	public static function incrementParents($cID) {
		$db = Loader::db();
		$cParentID = $db->GetOne("select cParentID from Pages where cID = ?", array($cID));

		$q = "update Pages set cChildren = cChildren+1 where cID = ?";

		$cpc = Page::getByID($cParentID);
		$cpc->refreshCache();

		$r = $db->query($q, array($cParentID));

	}

	/**
	 * For a particular page ID, grabs all the pages of this page, and decrements the cTotalChildren number for them
	 */
	public static function decrementParents($cID) {
		$db = Loader::db();
		$cParentID = $db->GetOne("select cParentID from Pages where cID = ?", array($cID));
		$cChildren = $db->GetOne("select cChildren from Pages where cID = ?", array($cParentID));
		$cChildren--;
		if ($cChildren < 0) {
			$cChildren = 0;
		}

		$q = "update Pages set cChildren = ? where cID = ?";

		$cpc = Page::getByID($cParentID);
		$cpc->refreshCache();

		$r = $db->query($q, array($cChildren, $cParentID));

	}

	/**
	 * Returns the total number of pages created for a given date
	 */
	public static function getTotalPagesCreated($date) {
		$db = Loader::db();
		$num = $db->GetOne('select count(Pages.cID) from Pages inner join Collections on Pages.cID = Collections.cID where cDateAdded >= ? and cDateAdded <= ? and cIsSystemPage = 0 and cIsTemplate = 0', array($date . ' 00:00:00', $date . ' 23:59:59'));
		return $num;
	}


}
