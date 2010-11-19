<?php 
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */ 

defined('C5_EXECUTE') or die("Access Denied."); 

class ConcreteScrapbookHelper {  

	function getPersonalScrapbookName() {
		return 'userScrapbook';
	}

	function getGlobalScrapbookPage(){
		return Page::getByPath('/dashboard/scrapbook'); 
	}

	function getAvailableScrapbooks(){
		$db = Loader::db();
		$scrapbookPage = ConcreteScrapbookHelper::getGlobalScrapbookPage(); 
		return $db->getAll('SELECT arID, arHandle FROM Areas WHERE cID='.intval($scrapbookPage->getCollectionId()));
	}

	/** 
	 * Returns the default scrapbook to add blocks to. This is typically the last one added to.
	 */
	public function getDefault() {
		$sb = $_SESSION['ccmLastViewedScrapbook'];
		if ($sb == '') {
			return $this->getPersonalScrapbookName();
		}
		return $sb;
	}
	
	/** 
	 * Sets the default scrapbook to add blocks to. This is typically the last one added to.
	 */
	public function setDefault($scrapbook) {
		$_SESSION['ccmLastViewedScrapbook'] = $scrapbook;
	}


}

?>