<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUpgradeVersion551Helper {

	public function run() {
		Loader::model('single_page');
		$sp = Page::getByPath('/dashboard/system/basics/interface');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/system/basics/interface');
			$d1a->update(array('cName'=>t('Interface Preferences')));
		}
		$sp = Page::getByPath('/dashboard/news');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/news');
			$d1a->update(array('cName'=>t('Newsflow')));
			$d1a->setAttribute('exclude_nav', 1);
			$d1a->setAttribute('exclude_search_index', 1);
		}
	}

	public function prepare() {
		// we install the updated schema just for tables that matter
		//Package::installDB(dirname(__FILE__) . '/db/version_551.xml');
	}

	
}