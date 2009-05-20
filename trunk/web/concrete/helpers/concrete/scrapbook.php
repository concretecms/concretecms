<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Tony Trupp <tony@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */ 

defined('C5_EXECUTE') or die(_("Access Denied.")); 

class ConcreteScrapbookHelper {  

	function getGlobalScrapbookPage(){
		return Page::getByPath('/dashboard/scrapbook'); 
	}

	function getAvailableScrapbooks(){
		$db = Loader::db();
		$scrapbookPage = ConcreteScrapbookHelper::getGlobalScrapbookPage(); 
		return $db->getAll('SELECT arID, arHandle FROM Areas WHERE cID='.intval($scrapbookPage->getCollectionId()));
	}

}

?>