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
class ConcreteUpgradeVersion542Helper {

	public function run() {
		$db = Loader::db();
		$cnt = $db->GetOne('select count(*) from TaskPermissions where tpHandle = ?', array('delete_user'));
		if ($cnt < 1) {
			$g3 = Group::getByID(ADMIN_GROUP_ID);
			$tip = TaskPermission::addTask('delete_user', t('Delete Users'), false);
			if (is_object($g3)) {
				$tip->addAccess($g3);
			}
		}
		Loader::model('single_page');
		$sp = Page::getByPath('/dashboard/settings/multilingual');
		if ($sp->isError()) {
			$d1a = SinglePage::add('/dashboard/settings/multilingual');
			$d1a->update(array('cName'=>t('Multilingual Setup')));
		}
		$sp = Page::getByPath('/dashboard/composer');
		if ($sp->isError()) {
			$d2 = SinglePage::add('/dashboard/composer');
			$d2->update(array('cName'=>t('Composer Beta'), 'cDescription' => t('Write for your site.')));
		}
		$sp = Page::getByPath('/dashboard/composer/write');
		if ($sp->isError()) {
			$d3 = SinglePage::add('/dashboard/composer/write');
		}
		$sp = Page::getByPath('/dashboard/composer/drafts');
		if ($sp->isError()) {
			$d4 = SinglePage::add('/dashboard/composer/drafts');
		}
		$sp = Page::getByPath('/dashboard/pages/types/composer');
		if ($sp->isError()) {
			$d5 = SinglePage::add('/dashboard/pages/types/composer');
		}
	}
	
	public function prepare() {
		// we install the updated schema just for tables that matter
		Package::installDB(dirname(__FILE__) . '/db/version_542.xml');
	}

	
}