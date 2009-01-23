<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Pages
 * @access private
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * An object inherited by the dashboard home page, which uses a variety of modules with special capabilities
 * @access private
 * @package Pages
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class DashboardHomepageView extends View {

	private function getDisplayOrder($mParentID) {
		$db = Loader::db();
		$num = $db->GetOne("select count(dbhID) from DashboardHomepage");
		if ($num > 0) {
			return $num;
		} else {
			return '0';
		}
	}
	
	public static function getByHandle($dbhModule) {
		$dbh = new DashboardHomepage();
		$dbh->Load('dbhModule = ?', array($dbhModule));
		return $dbh;
	}

	public function add($module, $displayName, $pkg = null) {
		$th = new DashboardHomepage();
		$th->pkgID = (is_object($pkg)) ? $pkg->getPackageID() : '0';
		$th->dbhModule = $module;
		$th->dbhDisplayName = $displayName;
		$th->dbhDisplayOrder = '0';
		$th->Insert();
		return $th;		
	}
	
	public function getModules() {
		$dbh = new DashboardHomepage();
		$modules = $dbh->Find('1=1 order by dbhDisplayOrder asc');
		return $modules;
	}

	public function output($dbh) {
		ob_start();
		if ($dbh->pkgID > 0) {
			$pkg = Package::getByID($dbh->pkgID);
			Loader::dashboardModule($dbh->dbhModule, $pkg);
		} else {
			Loader::dashboardModule($dbh->dbhModule);
		}
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

/* 
 * @access private
 */
class DashboardHomepage extends ADODB_Active_Record {

	public $_table = "DashboardHomepage";
	

}
