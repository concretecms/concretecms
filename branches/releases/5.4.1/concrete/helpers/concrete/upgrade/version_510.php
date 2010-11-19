<?php 
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
class ConcreteUpgradeVersion510Helper {
	
	public function run() {
		// Since 5.1.0 we've moved around a number of pages in the dashboard
		Loader::model('single_page');
		// Rename Forms to Reports
		$p = Page::getByPath('/dashboard/form_results');
		
		// We can only run these once so we do a check to see if that's the case.
		if ($p->isError()) {
			return false;
		}
		
		$p->update(array('cName' => t('Reports'), 'cDescription'=>t('Get data from forms and logs.'), 'cHandle' => 'reports'));	
		$p->rescanCollectionPath();
		$p = SinglePage::getByID($p->getCollectionID());
		$p->refresh();

		$d3a = SinglePage::add('/dashboard/reports/forms');
		$d3b = SinglePage::add('/dashboard/reports/logs');
		$d3c = SinglePage::add('/dashboard/reports/database');
		
		$d4 = Page::getByPath('/dashboard/users');
		$d4a = SinglePage::add('/dashboard/users/search');
		$d4b = SinglePage::add('/dashboard/users/add');
		$d4c = SinglePage::add('/dashboard/users/groups');
		$d4d = Page::getByPath("/dashboard/users/attributes");
		
		$db = Loader::db();
		$db->query("update Pages set cDisplayOrder = 0 where cID = ?", array($d4a->getCollectionID()));
		$db->query("update Pages set cDisplayOrder = 1 where cID = ?", array($d4b->getCollectionID()));
		$db->query("update Pages set cDisplayOrder = 2 where cID = ?", array($d4c->getCollectionID()));
		$db->query("update Pages set cDisplayOrder = 3 where cID = ?", array($d4d->getCollectionID()));
		
		$p = Page::getByPath('/dashboard/groups');
		$p->delete();
		
		$p = Page::getByPath('/dashboard/collection_types');
		$p->update(array('cHandle' => 'pages'));	
		$p->rescanCollectionPath();
		$p = SinglePage::getByID($p->getCollectionID());
		$p->refresh();

		$p = Page::getByPath('/dashboard/pages/attributes');
		$p->delete();
		
		$d7a = SinglePage::add('/dashboard/pages/themes');
		$d7b = SinglePage::add('/dashboard/pages/themes/add');
		$d7c = SinglePage::add('/dashboard/pages/themes/inspect');
		$d7d = SinglePage::add('/dashboard/pages/themes/customize');
		$d7e = SinglePage::add('/dashboard/pages/themes/marketplace');
		$d7f = SinglePage::add('/dashboard/pages/types');
		$d7g = SinglePage::add('/dashboard/pages/types/attributes');
		$d7h = SinglePage::add('/dashboard/pages/single');

		$p = Page::getByPath('/dashboard/themes');
		$p->delete();

		$d3a->update(array('cName'=>t('Form Results'), 'cDescription'=>t('Get submission data.')));
		$d4->update(array('cName'=>t('Users and Groups'), 'cDescription'=>t('Add and manage people.')));

		$d4a->update(array('cName'=>t('Find Users')));
		$d4b->update(array('cName'=>t('Add User')));
		$d4c->update(array('cName'=>t('Groups')));
		$d4d->update(array('cName'=>t('User Attributes')));

		$d7 = Page::getByPath('/dashboard/pages');
		$d7->update(array('cName' => t('Pages and Themes'), 'cDescription'=>t('Reskin your site.')));
		$d7f->update(array('cName'=>t('Page Types'), 'cDescription'=>t('What goes in your site.')));	
		$d7h->update(array('cName'=>t('Single Pages')));

		$p = Page::getByPath('/dashboard/logs');
		$p->delete();
		
	}
	
}
		
	