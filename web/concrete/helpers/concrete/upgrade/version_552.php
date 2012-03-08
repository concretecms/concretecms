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
class ConcreteUpgradeVersion552Helper {

	public function run() {
		Loader::model('single_page');
		$sp = Page::getByPath('/dashboard/system/permissions/users');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/system/permissions/users');
			$d1a->update(array('cName'=>t('User Permissions')));
		}
		$sp = Page::getByPath('/dashboard/system/permissions/advanced');
		if ($sp->isError()) {
			$d1b = SinglePage::add('/dashboard/system/permissions/advanced');
			$d1b->update(array('cName'=>t('Advanced Permissions')));
		}
	}

	
}