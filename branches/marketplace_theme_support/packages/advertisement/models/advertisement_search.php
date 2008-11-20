<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Utilities
 * @category Concrete
 * @author Ryan Tyler <ryan@concretecms.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * @access private
 */

/**
 * @access private
 */
Loader::library('search');

class AdvertisementSearch extends Search {
	
	function __construct($s) {
		$db = Loader::db();
		
		$this->searchQuery = "
			SELECT DISTINCT ad.* 
			FROM btAdvertisementDetails ad LEFT JOIN btAdvertisementToGroups atg on ad.aID = atg.aID ";
		$this->validSortColumns = "name,clickThrus,targetImpressions,targetClickThrus,impressions";
		global $db;
		if ($s['agID']) {
			
			$this->setLinkingWord();
			$this->filters .= "atg.agID = " . $s['agID'];
			
		}
		
	}

}

?>