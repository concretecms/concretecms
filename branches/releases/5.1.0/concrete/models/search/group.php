<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * @access private
 */
 
/** 
 * @access private
 */
Loader::library('search');

class GroupSearch extends Search {

	function GroupSearch($searchArray) {
		
		$db = Loader::db();
		
		//$this->totalQuery = "select count(*) as total from Users";			
		$this->searchQuery = "select Groups.gID, Groups.gName, Groups.gDescription from Groups";
		
		$this->validSortColumns = "gName, gDescription, gID";
		$this->setLinkingWord();
		$this->filters .= 'Groups.giD > ' . REGISTERED_GROUP_ID;
		
		if ($searchArray['gKeywords']) {
			$this->setLinkingWord();
			$this->filters .= "(Groups.gName like " . $db->qstr('%' . $searchArray['gKeywords'] . '%') . " or Groups.gDescription like " . $db->qstr('%' . $searchArray['gKeywords'] . '%') . ")";
		}
		
		$this->total = $this->getTotal();		
		return $this;
	}
}