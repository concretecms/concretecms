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
class ConcreteUpgradeVersion530Helper {
	
	public function run() {
		$db = Loader::db();
		Loader::model('collection_attributes');
		
		//add the new collection attribute keys
		$cak=CollectionAttributeKey::getByHandle('header_extra_content');
		if( !intval($cak->getAttributeKeyID()) )
			CollectionAttributeKey::add('header_extra_content', t('Header Extra Content'), true, null, 'TEXT');
		$cak=CollectionAttributeKey::getByHandle('exclude_search_index');
		if( !intval($cak->getAttributeKeyID()) )
			CollectionAttributeKey::add('exclude_search_index', t('Exclude From Search Index'), true, null, 'BOOLEAN');
		
	}
	
}
		
	