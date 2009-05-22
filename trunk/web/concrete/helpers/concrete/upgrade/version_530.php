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
		
		// Add in stuff that may have gotten missed before
		$p = Page::getByPath('/profile');
		if ($p->isError()) {
			$d1 = SinglePage::add('/profile');
			$d2 = SinglePage::add('/profile/edit');
			$d3 = SinglePage::add('/profile/avatar');				
		}
		$p2 = Page::getByPath('/dashboard/users/registration');
		if ($p2->isError()) {
			$d4 = SinglePage::add('/dashboard/users/registration');
		}
		
		// Move any global blocks to new scrapbook page.
		$sc = Page::getByPath("/dashboard/scrapbook/global");
		$scn = Page::getByPath('/dashboard/scrapbook');
		$scu = Page::getByPath('/dashboard/scrapbook/user');
		if (!$sc->isError()) {
			$blocks = $sc->getBlocks("Global Scrapbook");
			if (count($blocks) > 0) {
				// we create the new shared scrapbook 1
				$a = Area::getOrCreate($scn, t('Shared Scrapbook 1'));
				foreach($blocks as $_b) {
					// we move them into the area on the new page. 
					$_b->move($scn, $a);					
				}
			}
			$sc->delete();
		}
		if (!$scu->isError()) {
			$scu->delete();
		}
		//add the new collection attribute keys
		$cak=CollectionAttributeKey::getByHandle('header_extra_content');
		if( !intval($cak->getAttributeKeyID()) )
			CollectionAttributeKey::add('header_extra_content', t('Header Extra Content'), true, null, 'TEXT');
		$cak=CollectionAttributeKey::getByHandle('exclude_search_index');
		if( !intval($cak->getAttributeKeyID()) )
			CollectionAttributeKey::add('exclude_search_index', t('Exclude From Search Index'), true, null, 'BOOLEAN');
		
	}
	
}
		
	