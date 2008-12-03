<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * @package Utilities
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * @access private
 */

/**
 * @access private
 */
Loader::library('search');

class FileSearch extends Search {

	function FileSearch($searchArray) {
		$db = Loader::db();

		$this->searchQuery = "select Blocks.bID, btFile.generictype, btFile.type, btFile.filename, btFile.origfilename, btFile.url, Blocks.bIsActive, Blocks.bDateAdded, Blocks.bDateModified, Users.uName, Users.uID from btFile
			inner join Blocks on (Blocks.bID = btFile.bID)
			inner join Users on (Users.uID = Blocks.uID)";
		
		$this->validSortColumns = "origfilename,filename,fileType,bDateAdded,uName";
		
		if ($searchArray['uName']) {
			$this->setLinkingWord();
			$this->filters .= "Users.uName = " . $db->qstr($searchArray['uName']);
		}

		if ($searchArray['bDateAdded']) {
			$this->setLinkingWord();
			$date = date('Y-m-d', strtotime($searchArray['bDateAdded']));
			$this->filters .= "DATE_FORMAT(Blocks.bDateAdded, '%Y-%m-%d') >= " . $db->qstr($date);
		}

		if ($searchArray['bFile']) {
			$this->setLinkingWord();
			$this->filters .= "btFile.filename like " . $db->qstr('%' . $searchArray['bFile'] . '%');
		}
		
		if ($searchArray['type']) {
			$this->setLinkingWord();
			$this->filters .= "btFile.generictype = " . $db->qstr($searchArray['type']);
		}

		$this->total = $this->getTotal();
		return $this;
	}

	function getFileTypes() {
		$filetypes = array();
		$db = Loader::db();
		$r = $db->query("select generictype from btFile group by generictype");
		if ($r) {
			while ($row = $r->fetchRow()) {
				$filetypes[] = $row['generictype'];
			}
		}

		return $filetypes;
	}

}

?>