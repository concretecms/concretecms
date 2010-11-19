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
class ConcreteUpgradeVersion533Helper {

	public function run() {
		$db = Loader::db();
		Cache::disableLocalCache();
		Loader::model('attribute/categories/collection');
		$cak = CollectionAttributeKey::getByHandle('exclude_page_list');
		if (!is_object($cak)) {
			$boolt = AttributeType::getByHandle('boolean');
			$cab4b = CollectionAttributeKey::add($boolt, array('akHandle' => 'exclude_page_list', 'akName' => t('Exclude From Page List'), 'akIsSearchable' => true));
			
			Loader::model('page_list');
			$pl = new PageList();
			$pl->filterByExcludeNav(1);
			$list = $pl->get();
			foreach($list as $c) {
				$c->setAttribute('exclude_page_list', 1);
				$c->reindex();
			}
		}
		
		Cache::enableLocalCache();
	}
	

		
}
		
	