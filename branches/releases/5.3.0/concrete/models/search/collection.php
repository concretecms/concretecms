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

class CollectionSearch extends Search {

	var $cIDArray = array();
	
	function CollectionSearch($searchArray) {

		// we have to search PagePermissions because we need to be sure that
		// we can view the collections we're going to be adding to
		$db = Loader::db();
		
		/*$this->totalQuery = "select count(DISTINCT Collections.cID) as total from PagePermissions
			left join Collections on (PagePermissions.cID = Collections.cID)
			left join PageTypes on (Collections.ctID = PageTypes.ctID)";*/

		$this->searchQuery = "select distinct Pages.cID, Users.uName, cv1.cvName, cv1.cvDescription, Collections.cDateAdded, Pages.cParentID, Collections.cDateModified, PageTypes.ctName, PageTypes.ctHandle from Pages
			inner join Users on (Pages.uID = Users.uID)
			inner join Collections on (Collections.cID = Pages.cID)
			left join CollectionVersions cv1 on (cv1.cID = Pages.cID and cv1.cvIsApproved = 1)
			inner join PageTypes on (Pages.ctID = PageTypes.ctID)";

		$this->setLinkingWord();
		$this->filters .= "cv1.cvIsApproved = 1";
		
		$this->validSortColumns = "cID,cvName,cvDescription,cDateAdded,cDateModified";

		if ($searchArray['ctHandle']) {
			$this->setLinkingWord();
			if (is_array($searchArray['ctHandle'])) {
				$inStr = '(';
				$i = 0;
				foreach($searchArray['ctHandle'] as $handle) {
			 		$inStr .= ($i != 0) ? ', ' : '';
					$inStr .= $db->qstr($handle);
					$i++;
				}
				$inStr .= ')';


				$this->filters .= "PageTypes.ctHandle in {$inStr}";
			} else {
				$this->filters .= "PageTypes.ctHandle = " . $db->qstr($searchArray['ctHandle']);
			}
		}

		if($searchArray['cStartDate']) {
			$this->setLinkingWord();
			$sd = date('Y-m-d', strtotime($searchArray['cStartDate']));
			$this->filters .="Collections.cDateAdded >= '{$sd} 00:00:00'";
		}

		if($searchArray['cEndDate']) {
			$this->setLinkingWord();
			$ed = date('Y-m-d', strtotime($searchArray['cEndDate']));
			$this->filters .="Collections.cDateAdded <= '{$ed} 23:59:59'";
		}
		
		if ($searchArray['cName']) {
			$this->setLinkingWord();
			$cName = $db->qstr('%' . $searchArray['cName'] . '%');
			$this->filters .= "cv1.cvName like " . $cName;
		}
		
		if ($searchArray['cChildrenSelect'] && is_numeric($searchArray['cChildren'])) {
			$this->setLinkingWord();
			switch($searchArray['cChildrenSelect']) { 
				case "lt":
					$this->filters .= "Pages.cChildren < " . intval($searchArray['cChildren']);
					break;
				case "gt":
					$this->filters .= "Pages.cChildren > " . intval($searchArray['cChildren']);
					break;
				default:
					$this->filters .= "Pages.cChildren = " . intval($searchArray['cChildren']);
					break;
			}
		}
		
		if ($searchArray['cDescription']) {
			$this->setLinkingWord();
			$this->filters .= "cv1.cvDescription like " . $db->qstr('%' . $searchArray['cDescription'] . '%');
		}

		if ($searchArray['cKeywords']) {
			// combining both cName and cDescription into one inclusive field
			$this->setLinkingWord();
			$db = Loader::db();
			$ck = $db->qstr('%' . $searchArray['cKeywords'] . '%');
			
			$this->filters .= "(cv1.cvDescription like {$ck} or cv1.cvName like {$ck})";
		}
		
		if ($searchArray['uID']) {
			$this->setLinkingWord();
			$this->filters .= "Pages.uID = " . intval($searchArray['uID']);
		}

		if ($searchArray['uName']) {
			$this->setLinkingWord();
			$this->filters .= "Users.uName = '{$searchArray['uName']}'";
		}
		if ($searchArray['uVersionCreator']) {
			$db = Loader::db();
			$v = array($searchArray['uVersionCreator']);
			$uid = $db->getOne("select uID from Users where uName = ?", $v);
			if ($uid > 0) {
				$this->searchQuery .= " left join CollectionVersions cv2 on (cv2.cID = Pages.cID and cv2.cvAuthorUID = {$uid})";
				$this->setLinkingWord();
				$this->filters .= "cv2.cvAuthorUID = {$uid}";
			} else {
				$this->setLinkingWord();
				$this->filters .= "1 <> 1";
			}
		}
		
		if ($searchArray['ctID']) {
			$this->setLinkingWord();
			$this->filters .= "Pages.ctID = " . intval($searchArray['ctID']);
		}

		$this->setLinkingWord();
		$this->filters .= "Pages.cIsTemplate = 0";
		
		// we have to see if we have read access to it (if we're not a super user. If we are, disregard)

		$this->total = $this->getTotal();
		
		return $this;
	}
	//reset cIDArray so there won't be junk inside.
	function resetCollectionIDArray() {$this->cIDArray=array();}
	
	//This function will recure until it has populated an array with all the cID's of parent pages
	//a bread crumb
	function populateCollectionIDArray($theID) {
		//print "{" . $theID . "}";
		$db = Loader::db();
		$theID = intval($theID);
		$v = array($theID);
		$q = "select cParentID from Pages where cID = {$theID}";
		$r = $db->query($q);
		while($row = $r->fetchRow()){
			if(in_array($row['cParentID'], $this->cIDArray)) {
				break;
			} else {
				$this->cIDArray[] = $row['cParentID'];
				$this->populateCollectionIDArray($row['cParentID']);
			}
		}
	}
	
}

?>