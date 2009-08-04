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

defined('C5_EXECUTE') or die(_("Access Denied."));
class ConcreteUpgradeVersion532Helper {

	//run before the db.xml changes take place
	public function prepare() {
		
	}
	
	public function run() {
		Loader::model('collection_attributes');
		//add the new collection attribute keys
		
		$cak=CollectionAttributeKey::getByHandle('exclude_sitemapxml');
		if( !intval($cak->getAttributeKeyID()) ) [
			$cak = CollectionAttributeKey::add('exclude_sitemapxml', t('Exclude From sitemap.xml'), true, null, 'BOOLEAN');
		}
		
		//change the page/tab name of the dashboard users registration page
		$dashboardRegistrationPage=Page::getByPath('/dashboard/users/registration');
		if( intval($dashboardRegistrationPage->cID) ) 
			$dashboardRegistrationPage->update(array('cName'=>t('Login & Registration')));
		Config::save('LOGIN_ADMIN_TO_DASHBOARD', 1);
	
		//profile friends page install	
		Loader::model('single_page');
		$friendsPage=Page::getByPath('/profile/friends');
		if( !intval($friendsPage->cID) ) 
			SinglePage::add('/profile/friends');
	}
	
}
		
	