<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

/**
 * The user attribute key object represents a metadata key for a user, like "birth date" or "user." Administrators can create any number of these keys and apply them to user accounts, and can do so graphically.
 *
 * @package Users
 * @category Concrete
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

class UserAttributeKey extends Object {
	
	
	function get($ukID) {
		if (!is_numeric($ukID)) {
			return false;
		}
		
		$db = Loader::db();
		$a = array($ukID);
		$q = "select ukID, ukHandle, ukName, ukRequired, ukHidden, ukPrivate, ukDisplayedOnRegister, ukValues, ukType from UserAttributeKeys where ukID = ?";
		$r = $db->query($q, $a);
	
		if ($r) {
			$cak = new UserAttributeKey;
			$row = $r->fetchRow();
			foreach($row as $k => $v) {
				$cak->{$k} = $v;
			}
			return $cak;
		}
	}
	
	function getByHandle($ukHandle) {
		$db = Loader::db();
		$a = array($ukHandle);
		$q = "select ukID, ukHandle, ukRequired, ukName, ukHidden, ukPrivate, ukDisplayedOnRegister, ukValues, ukType from UserAttributeKeys where ukHandle = ?";
		$r = $db->query($q, $a);
		
	
		if ($r) {
			$cak = new UserAttributeKey;
			$row = $r->fetchRow();
			if (is_array($row)) {
				foreach($row as $k => $v) {
					$cak->{$k} = $v;
				}
				return $cak;
			}
		}
	}
	
	function getKeyID() {return $this->ukID;}
	function getKeyHandle() {return $this->ukHandle;}
	function getKeyName() {return $this->ukName;}
	function isKeyHidden() {return $this->ukHidden;}
	function getKeyValues() {return $this->ukValues;}
	function getKeyType() {return $this->ukType;}
	function isKeyRequired() {return $this->ukRequired;}
	function isKeyPrivate() {return $this->ukPrivate;}
	function isKeyDisplayedOnRegister() {return $this->ukDisplayedOnRegister;}

	function getNumEntries() {
		$db = Loader::db();
		$num = $db->getOne("select count(ukID) from UserAttributeValues where ukID = {$this->ukID}");
		return $num;
	}

	function delete() {
		// this removes the record from the CAKeys table, and from the CTypeAttributes tables, but
		// not from the actual CAValues table, nor from the lookup columns
		$db = Loader::db();
		$a = array($this->getKeyID());
		$db->query("delete from UserAttributeKeys where ukID = ?", $a);
		$db->query("delete from UserAttributeValues where ukID = ?", $a);		
	}
	
	function inUse($ukHandle) {
		$db = Loader::db();
		$a = array($ukHandle);
		$q = "select ukID from UserAttributeKeys where ukHandle = ?";
		$ukID = $db->getOne($q, $a);
		if ($ukID > 0) {
			return true;
		}
	}
	

	function add($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType) {
		$db = Loader::db();
		$a = array($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType);
		$r = $db->query("insert into UserAttributeKeys (ukHandle, ukName, ukRequired, ukPrivate, ukDisplayedOnRegister, ukHidden, ukValues, ukType) values (?, ?, ?, ?, ?, ?, ?, ?)", $a);
		if ($r) {
			$ukID = $db->Insert_ID();
			
			$ak = UserAttributeKey::get($ukID);
			if (is_object($ak)) {
				return $ak;
			}
		}
	}
	
	function update($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType) {
		$db = Loader::db();
		$a = array($ukHandle, $ukName, $ukRequired, $ukPrivate, $ukDisplayedOnRegister, $ukHidden, $ukValues, $ukType, $this->ukID);
		$db->query("update UserAttributeKeys set ukHandle = ?, ukName = ?, ukRequired = ?, ukPrivate = ?, ukDisplayedOnRegister = ?, ukHidden = ?, ukValues = ?, ukType = ? where ukID = ?", $a);
		
		$ak = UserAttributeKey::get($this->ukID);
		if (is_object($ak)) {
			return $ak;
		}
	}
	
	function updateAttributesDisplayOrder($uats) {
		$db = Loader::db();
		for ($i = 0; $i < count($uats); $i++) {
			$v = array($uats[$i]);
			$db->query("update UserAttributeKeys set displayOrder = {$i} where ukID = ?", $v);
		}
	}
	
	function getRequiredKeys() {
		// these are keys that are active and required
		$q = "select ukID from UserAttributeKeys where ukHidden = 0 and ukRequired = 1 order by displayOrder asc";
		$db = Loader::db();
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = UserAttributeKey::get($row['ukID']);
		}
		return $la; 
	}
	
	function getList($displayHidden = false) {
		$db = Loader::db();
		$q = "select ukID from UserAttributeKeys ";
		if (!$displayHidden) {
			$q .= "where ukHidden = 0 ";
		}
		$q .= "order by displayOrder asc";
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = UserAttributeKey::get($row['ukID']);
		}
		return $la;
	}
	
	function getRegistrationList() {
		$db = Loader::db();
		$q = "select ukID from UserAttributeKeys where ukHidden = 0 and ukDisplayedOnRegister = 1 order by displayOrder asc";
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = UserAttributeKey::get($row['ukID']);
		}
		return $la;
	}
	
	function getPublicList() {
		$db = Loader::db();
		$q = "select ukID from UserAttributeKeys where ukHidden = 0 and ukPrivate = 0 order by displayOrder asc";
		$r = $db->query($q);
		$la = array();
		while ($row = $r->fetchRow()) {
			$la[] = UserAttributeKey::get($row['ukID']);
		}
		return $la;
	}
	
	function validateSubmittedRequest() {
		$return = array();
		$list = UserAttributeKey::getRequiredKeys();
		foreach($list as $l) {
			if (!trim($_REQUEST[$l->getFormElementName()])) {
				$return[] = $l->getKeyName();
			}
		}		
		return $return;
	}
	
	function saveValue($uID, $value) {
		$db = Loader::db();
		$v = array($this->getKeyID(), $uID);
		$cnt = $db->getOne("select count(ukID) from UserAttributeValues where ukID = ? and uID = ?", $v);
	
		array_unshift($v, $value);

		if ($cnt > 0) {
			$db->query("update UserAttributeValues set value = ? where ukID = ? and uID = ?", $v);
		} else {
			$db->query("insert into UserAttributeValues (value, ukID, uID) values (?, ?, ?)", $v);
		}
	}
	
	function getUserValue($uID) {
		$db = Loader::db();
		$r = $db->query("select ukType, value from UserAttributeValues inner join UserAttributeKeys on UserAttributeValues.ukID = UserAttributeKeys.ukID where uID = {$uID} and UserAttributeKeys.ukID = {$this->ukID}");
		if ($r) {
			$row = $r->fetchRow();
			if (is_array($row)) {
				switch($row['ukType']) {
					case "BOOLEAN":
						return ($row['value'] == 1) ? "Yes" : "No";
						break;
					default:
						return $row['value'];
						break;
				}
						
			}
		}
	}
	
	function outputHTML($uID = null) {
		$ir = (isset($_REQUEST[$this->getFormElementName()]));
		
		$text = "";
		$db = Loader::db();
		if ($uID) {
			$text = $db->getOne("select value from UserAttributeValues where uID = {$uID} and ukID = {$this->ukID}");
		}
		if ($ir) {
			$text = htmlentities($_REQUEST[$this->getFormElementName()]);
		}
		switch($this->ukType) {
			case "TEXT":
				return '<input type="text" name="' . $this->getFormElementName() . '" id="' . $this->getFormElementName() . '" value="' . $text . '" class="uak-text" />';
				break;
			case "TEXTAREA":
				return '<textarea id="' . $this->getFormElementName() . '" name="' . $this->getFormElementName() . '" class="uak-textarea">' . $text . '</textarea>';
				break;
			case "BOOLEAN":
				$checked = ($text == 1) ? 'checked' : '';
				return '<input type="checkbox" name="' . $this->getFormElementName() . '" id="' . $this->getFormElementName() . '" value="1" ' . $checked . ' class="uak-checkbox"/>';
				break;
			case "SELECT":
				$r = '<select name="' . $this->getFormElementName() . '" id="' . $this->getFormElementName() . '" class="uak-select">';
				//$r .= '<option value="">N/A</option>';
				$arr = preg_split("/\r|\n/", trim($this->getKeyValues()), -1, PREG_SPLIT_NO_EMPTY);
				foreach($arr as $v) {
					$selected = ($text == $v) ? 'selected' : '';
					$r .= '<option value="' . $v . '" ' . $selected . ' >' . $v . '</option>';
				}
				$r .= '</select>';
				return $r;
				break;
			case "HTML":
				return $this->getKeyValues();
				break;
			case "RADIO":
				$arr = preg_split("/\r|\n/", trim($this->getKeyValues()), -1, PREG_SPLIT_NO_EMPTY);
				$checked = ($text == '') ? 'checked' : '';
				//$r = '<div class="uak-radio"><input type="radio" name="uak_' . $this->ukID . '" value="" ' . $checked . ' /> N/A</div>';			
				$i = 1;
				foreach($arr as $v) {
					$checked = ($text == $v) ? 'checked' : '';
					$r .= '<div class="uak-radio"><input type="radio" name="' . $this->getFormElementName() . '" id="' . $this->getFormElementName() . '_' . $i . '" value="' . $v . '" ' . $checked . ' /> ' . $v . '</div>';			
					$i++;
				}
				
				return $r;
				break;
			
		}
		
	}
	
	function getFormElementName() {
		return "uak_" . $this->ukID;
	}
	
	function outputSearchHTML($uID = null) {
		$ir = (isset($_REQUEST[$this->getFormElementName()]));
		$text = "";
		if ($ir) {
			$text = htmlentities($_REQUEST[$this->getFormElementName()]);
		}
		switch($this->ukType) {
			case "TEXT":
				return '<input type="text" name="' . $this->getFormElementName() . '" value="' . $text . '" class="uak-text" />';
				break;
			case "TEXTAREA":
				return '<input type="text" name="' . $this->getFormElementName() . '" value="' . $text . '" class="uak-text" />';
				break;
			case "BOOLEAN":
				$checked = ($text == 1) ? 'checked' : '';
				return '<input type="checkbox" name="' . $this->getFormElementName() . '" value="1" ' . $checked . ' />';
				break;
			case "SELECT":
				$r = '<select name="' . $this->getFormElementName() . '" class="uak-select">';
				$r .= '<option value="">' . t('N/A') . '</option>';
				$arr = preg_split("/\r|\n/", trim($this->getKeyValues()), -1, PREG_SPLIT_NO_EMPTY);
				foreach($arr as $v) {
					$selected = ($text == $v) ? 'selected' : '';
					$r .= '<option value="' . $v . '" ' . $selected . ' >' . $v . '</option>';
				}
				$r .= '</select>';
				return $r;
				break;
			case "RADIO":
				$arr = preg_split("/\r|\n/", trim($this->getKeyValues()), -1, PREG_SPLIT_NO_EMPTY);
				$checked = ($text == '') ? 'checked' : '';
				$r = '<div class="uak-radio"><input type="radio" name="' . $this->getFormElementName() . '" value="" ' . $checked . ' /> N/A</div>';			
				foreach($arr as $v) {
					$checked = ($text == $v) ? 'checked' : '';
					$r .= '<div class="uak-radio"><input type="radio" name="' . $this->getFormElementName() . '" value="' . $v . '" ' . $checked . ' /> ' . $v . '</div>';			
				}
				return $r;
				break;
			
		}
	}
	
}