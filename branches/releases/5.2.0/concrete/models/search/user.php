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
Loader::model('user_attributes');
 
class UserSearch extends Search {

	function UserSearch($searchArray) {
		
		$db = Loader::db();
		
		//$this->totalQuery = "select count(*) as total from Users";			
		$this->searchQuery = "select Users.uID, Users.uName, Users.uEmail, Users.uDateAdded from Users";
		
		$this->searchQuery = "select Users.uID, Users.uName, Users.uEmail, Users.uDateAdded, Users.uNumLogins from Users";
		$this->validSortColumns = "uName,uEmail,uDateAdded,uNumLogins";

		if ($searchArray['uName']) {
			$this->setLinkingWord();
			$this->filters .= "Users.uName like " . $db->qstr('%' . $searchArray['uName'] . '%');
		}
		
		if ($searchArray['uVal']) {
			$this->setLinkingWord();
			$this->filters .= "(Users.uName like " . $db->qstr('%' . $searchArray['uVal'] . '%') . " or Users.uEmail like " . $db->qstr('%' . $searchArray['uVal'] . '%') . ")";
		}
		
		if ($searchArray['uEmail']) {
			$this->setLinkingWord();			
			$this->filters .= "Users.uEmail like " . $db->qstr('%' . $searchArray['uEmail'] . '%');
		}
		
		if (VALIDATE_USER_EMAIL) {
			$uiv = array();
			if (is_array($searchArray['uIsValidated'])) {
				if (in_array(0, $searchArray['uIsValidated'])) {
					$uiv[] = 0;
				}
				if (in_array(1, $searchArray['uIsValidated'])) {
					$uiv[] = 1;
				}
			}
			
			if (count($uiv) == 0) {
				$uiv = array(-1, 0, 1);
			} else if (in_array(0, $uiv)) {
				$uiv[] = -1; // add the unknown flag as well
			}
	
			$this->setLinkingWord();
			$uivs = implode(',', $uiv);
			$this->filters .= "Users.uIsValidated in ({$uivs}) ";

			$ufr = array();
			if (is_array($searchArray['uIsFullRecord'])) {
				if (in_array(0, $searchArray['uIsFullRecord'])) {
					$ufr[] = 0;
				}
				if (in_array(1, $searchArray['uIsFullRecord'])) {
					$ufr[] = 1;
				}
			}
			
			if (count($ufr) == 0) {
				$ufr = array(0, 1);
			}
	
			$this->setLinkingWord();
			$ufrs = implode(',', $ufr);
			$this->filters .= "Users.uIsFullRecord in ({$ufrs}) ";
		}
		
		if ($searchArray['gID']) {
			$gIDStr = '';
			
			if (is_array($searchArray['gID'])) {
				foreach($searchArray['gID'] as $gID) {
					$_gID = intval($gID);
					$gIDStr .= $_gID . ',';
				}
				$gIDStr = substr($gIDStr, 0, strlen($gIDStr) - 1);
				
			} else {
				$gIDStr = intval($searchArray['gID']);
			}
			
			$this->searchQuery .= " left join UserGroups ug1 on (ug1.uID = Users.uID) and ug1.gID in ({$gIDStr}) ";
			$this->setLinkingWord();
			$this->filters .= "ug1.gID in ({$gIDStr})";
			
		}
		
		if ($searchArray['xgID']) {
			$xgIDStr = '';
			if (is_array($searchArray['xgID'])) {
				foreach($searchArray['xgID'] as $gID) {
					$_gID = intval($gID);
					$xgIDStr .= $_gID . ',';
				}
				$xgIDStr = substr($xgIDStr, 0, strlen($xgIDStr) - 1);
				
			} else {
				$xgIDStr = intval($searchArray['xgID']);
			}

			$this->searchQuery .= " left join UserGroups ug2 on (ug2.uID = Users.uID) and ug2.gID in ({$xgIDStr}) ";
			$this->setLinkingWord();
			$this->filters .= "ug2.gID is null ";
		}

		$dt = Loader::helper('form/date_time');
		$uDateAddedStart = $dt->translate('uDateAddedStart', $searchArray);
		$uDateAddedEnd = $dt->translate('uDateAddedEnd', $searchArray);
		
		if ($uDateAddedStart) {
			$this->setLinkingWord();
			$this->filters .= "Users.uDateAdded >= " . $db->qstr($uDateAddedStart);
		}
		
		if ($uDateAddedEnd) {
			$this->setLinkingWord();
			$this->filters .= "Users.uDateAdded <= " . $db->qstr($uDateAddedEnd);
		}

		$akeys = implode(':', array_keys($searchArray));
		if (preg_match('/uak/',$akeys)) {
			$i = 0;
			$attribs = UserAttributeKey::getList();
			foreach($attribs as $ak) {
				if ($searchArray['uak_' . $ak->getKeyID()]) {
					$i++;
					$akID = $ak->getKeyID();
					
					$this->searchQuery .= " inner join UserAttributeValues uak{$i} on (Users.uID = uak{$i}.uID and uak{$i}.ukID = {$akID}) ";
					$this->setLinkingWord();
					$this->filters .= "uak{$i}.value like " . $db->qstr($searchArray['uak_' . $ak->getKeyID()] . '%');
				}
			}
		}

		
		$this->total = $this->getTotal();		
		return $this;
	}
}