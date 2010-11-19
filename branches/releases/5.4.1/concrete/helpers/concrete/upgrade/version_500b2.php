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
class ConcreteUpgradeVersion500b2Helper {
	
	public function notes() {
		$arr = array();		
		return $arr;
	}
	
	public function run() {
		Loader::model("job");
		Loader::model('single_page');
		Job::installByHandle('generate_sitemap');
		$d1 = SinglePage::add('/download_file');
		if (is_object($d1)) {
			$d1->update(array('cName'=>'Download File'));
		}
		$d2 = SinglePage::add('/dashboard/logs');
		if (is_object($d2)) {
			$d2->update(array('cName'=>'Logging', 'cDescription' => 'Keep tabs on your site.'));
		}

	}
	
}
		
	