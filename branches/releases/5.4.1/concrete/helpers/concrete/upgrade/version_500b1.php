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
class ConcreteUpgradeVersion500b1Helper {
	
	public function notes() {
		$arr[] = 'Remove or empty your .htaccess file. It\'s format has changed. ';
		$arr[] = 'These blocks are now bundled with Concrete5. If any of the following blocks are installed in your local install, you may want to remove them locally, or at least their non-view templates:
		<blockquote>
		Guestbook<br/>
		Google Map<br/>
		RSS Display<br/>
		Search<br/></blockquote>';
		$arr[] = 'If you have any custom forms rename the "custom_form" directory to "external_form."';
		$arr[] = 'Edit your config/site.php file and remove the "SITE" definition from it. You may now change it through the settings interface in the dashboard.';
		
		return $arr;
	}
	
	public function run() {
		// Since we added the origfilename column in 5.0.0b1 we need to populate it
		Loader::block('library_file');
		$bl = new LibraryFileBlockController();
		$bl->populateOriginalFilenames();
		
		// install the new block types made available
		BlockType::installBlockType('flash_content');			
		BlockType::installBlockType('guestbook');			
		BlockType::installBlockType('slideshow');			
		BlockType::installBlockType('search');			
		BlockType::installBlockType('google_map');			
		BlockType::installBlockType('video');			
		BlockType::installBlockType('rss_displayer');			
		BlockType::installBlockType('youtube');			
		BlockType::installBlockType('survey');	
		
		// rename external form
		$bt = BlockType::getByHandle('custom_form');
		$db = Loader::db();
		$tables = $db->MetaTables('TABLES');
		if (isset($tables['btCustomForm']) && (!isset($tables['btExternalForm']))) {
			$db->Execute("alter table btCustomForm rename btExternalForm");
		}
		if (is_object($bt)) {
			BlockType::installBlockType('external_form', $bt->getBlockTypeID());
		}	
		// add new theme
		$th = PageTheme::getByHandle('greensalad');
		if (!is_object($th)) {
			PageTheme::add('greensalad');
		}
	}
	
}
		
	