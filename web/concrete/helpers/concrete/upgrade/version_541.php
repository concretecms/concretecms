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
class ConcreteUpgradeVersion541Helper {

	public function prepare() {
		// we install the updated schema just for tables that matter
		Package::installDB(dirname(__FILE__) . '/db/version_541.xml');
	}
	
	public function run() {
		BlockType::installBlockType('tags');			
		BlockType::installBlockType('next_previous');			
		BlockType::installBlockType('date_nav');
		
        Loader::model('collection_types');
        $blogEntry = CollectionType::getByHandle('blog_entry');
        if( !$blogEntry || !intval($blogEntry->getCollectionTypeID()) ){
            $data['ctHandle'] = 'blog_entry';
            $data['ctName'] = t('Blog Entry');
            $blogEntry = CollectionType::add($data);
        }
		
	}

	
}