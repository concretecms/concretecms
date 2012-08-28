<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helpful functions for working with navigating Concrete and other sites.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Navigation {

	/** 
	 * Returns a link to a page
	 * @param Page $cObj
	 * @return string $link
	 */
	public function getLinkToCollection(&$cObj, $appendBaseURL = false, $ignoreUrlRewriting = false) {
		// basically returns a link to a collection, based on whether or we have 
		// mod_rewrite enabled, and the collection has a path
		$dispatcher = '';
		if (!defined('URL_REWRITING_ALL') || URL_REWRITING_ALL == false) {
			if ((!URL_REWRITING) || $ignoreUrlRewriting) {
				$dispatcher = '/' . DISPATCHER_FILENAME;
			}
		}
		if ($cObj->getCollectionPath() != null) {
			$link = DIR_REL . $dispatcher . $cObj->getCollectionPath() . '/';
		} else {
			$_cID = ($cObj->getCollectionPointerID() > 0) ? $cObj->getCollectionPointerOriginalID() : $cObj->getCollectionID();
			if ($_cID > 1) {
				$link = DIR_REL . $dispatcher . '?cID=' . $_cID;
			} else {
				$link = DIR_REL . '/';
			}
		}
		
		if ($appendBaseURL) {
			$link = BASE_URL . $link;
		}
		return $link;
	}
	
	/** 
	 * Returns an array of collections as a breadcrumb to the current page
	 * @param Page $c
	 * @return array $cArray
	 */
	public function getTrailToCollection($c) {
		$db = Loader::db();

		$cArray = array();
		$currentcParentID = $c->getCollectionParentID();
		if ($currentcParentID > 0) {
			while (is_numeric($currentcParentID) && $currentcParentID > 0 && $currentcParentID) {
				$q = "select cID, cParentID from Pages where cID = '{$currentcParentID}'";
				$r = $db->query($q);
				$row = $r->fetchRow();
				if ($row['cID']) {
					$cArray[] = Page::getByID($row['cID'], 'ACTIVE');
				}

				$currentcParentID = $row['cParentID']; // moving up the tree until we hit 1
			}
		}

		return $cArray;
	}
	
	/**
	 * Returns the URL of a collection so that it can be clicked on
	 * @param Page $cObj
	 * @return string $link
	 */
	public function getCollectionURL($cObj) {
		return $this->getLinkToCollection($cObj, true);
	}

}