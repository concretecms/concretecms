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
	}
	
}
		
	