<?
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * An object that represents metadata added to users.
 * of metadata added to pages.
 * @author Andrew Embler <andrew@concrete5.org>
 * @package Users
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class UserAttributeKey extends AttributeKey {

	public function getIndexedSearchTable() {
		return 'UserSearchIndexAttributes';
	}

	public function getAttributes($uID, $method = 'getValue') {
		$db = Loader::db();
		$values = $db->GetAll("select avID, akID from UserAttributeValues where uID = ?", array($uID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = UserAttributeKey::getByID($val['akID']);
			if (is_object($ak)) {
				$value = $ak->getAttributeValue($val['avID']);
				$avl->addAttributeValue($ak, $value);
			}
		}
		return $avl;
	}
	
	public function getAttributeKeyDisplayOrder() {return $this->displayOrder;}

	
	public function load($akID) {
		parent::load($akID);
		$db = Loader::db();
		$row = $db->GetRow("select uakHidden, displayOrder, uakRequired, uakPrivate, uakDisplayedOnRegister from UserAttributeKeys where akID = ?", array($akID));
		$this->setPropertiesFromArray($row);
	}
	
	public function getAttributeValue($avID, $method = 'getValue') {
		$av = UserAttributeValue::getByID($avID);
		$av->setAttributeKey($this);
		return call_user_func_array(array($av, $method), array());
	}
	
	public static function getByID($akID) {
		$ak = new UserAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}

	public static function getByHandle($akHandle) {
		$db = Loader::db();
		$akID = $db->GetOne('select akID from AttributeKeys where akHandle = ?', array($akHandle));
		$ak = new UserAttributeKey();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;	
		}
	}
	
	public function isAttributeKeyRequired() {
		return $this->uakRequired;
	}
	public function isAttributeKeyDisplayedOnRegister() {
		return $this->uakDisplayedOnRegister;
	}
	public function isAttributeKeyPrivate() {
		return $this->uakPrivate;
	}
	public function isAttributeKeyHidden() {
		return $this->uakHidden;
	}
	
	public function sortListByDisplayOrder($a, $b) {
		if ($a->getAttributeKeyDisplayOrder() == $b->getAttributeKeyDisplayOrder()) {
			return 0;
		} else {
			return ($a->getAttributeKeyDisplayOrder() < $b->getAttributeKeyDisplayOrder()) ? -1 : 1;
		}
	}
	
	public static function getList() {
		$list = parent::getList('user');	
		usort($list, array('UserAttributeKey', 'sortListByDisplayOrder'));
		return $list;
	}
	
	/** 
	 * @access private 
	 */
	public function get($akID) {
		return UserAttributeKey::getByID($akID);
	}
	
	protected function saveAttribute($uo, $value = false) {
		// We check a cID/cvID/akID combo, and if that particular combination has an attribute value ID that
		// is NOT in use anywhere else on the same cID, cvID, akID combo, we use it (so we reuse IDs)
		// otherwise generate new IDs
		$av = $uo->getAttributeValueObject($this, true);
		parent::saveAttribute($av, $value);
		$db = Loader::db();
		$v = array($uo->getUserID(), $this->getAttributeKeyID(), $av->getAttributeValueID());
		$db->Replace('UserAttributeValues', array(
			'uID' => $uo->getUserID(), 
			'akID' => $this->getAttributeKeyID(), 
			'avID' => $av->getAttributeValueID()
		), array('cID', 'cvID', 'akID'));
		
		$uo->reindex();
	}
	
	public function add($akHandle, $akName, $akIsSearchable, $atID, $uakRequired, $uakDisplayedOnRegister, $uakPrivate, $uakHidden, $akIsAutoCreated = false, $akIsEditable = true) {
		$ak = parent::add('user', $akHandle, $akName, $akIsSearchable, false, $akIsAutoCreated, $akIsEditable, $atID);
		
		if ($uakRequired != 1) {
			$uakRequired = 0;
		}
		if ($uakDisplayedOnRegister != 1) {
			$uakDisplayedOnRegister = 0;
		}
		if ($uakPrivate != 1) {
			$uakPrivate = 0;
		}
		if ($uakHidden != 1) {
			$uakHidden = 0;
		}
		$db = Loader::db();
		$displayOrder = $db->GetOne('select max(displayOrder) from UserAttributeKeys');
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$displayOrder++;
		$v = array($ak->getAttributeKeyID(), $uakRequired, $uakDisplayedOnRegister, $uakPrivate, $uakHidden, $displayOrder);
		$db->Execute('insert into UserAttributeKeys (akID, uakRequired, uakDisplayedOnRegister, uakPrivate, uakHidden, displayOrder) values (?, ?, ?, ?, ?, ?)', $v);
		
		$nak = new UserAttributeKey();
		$nak->load($ak->getAttributeKeyID());
		return $nak;
	}
	
	public function update($akHandle, $akName, $akIsSearchable, $uakRequired, $uakDisplayedOnRegister, $uakPrivate, $uakHidden) {
		$ak = parent::update($akHandle, $akName, $akIsSearchable);
		if ($uakRequired != 1) {
			$uakRequired = 0;
		}
		if ($uakDisplayedOnRegister != 1) {
			$uakDisplayedOnRegister = 0;
		}
		if ($uakPrivate != 1) {
			$uakPrivate = 0;
		}
		if ($uakHidden != 1) {
			$uakHidden = 0;
		}
		$db = Loader::db();
		$v = array($uakRequired, $uakDisplayedOnRegister, $uakPrivate, $uakHidden, $ak->getAttributeKeyID());
		$db->Execute('update UserAttributeKeys set uakRequired = ?, uakDisplayedOnRegister= ?, uakPrivate = ?, uakHidden = ? where akID = ?', $v);
	}
	
	
	
	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from UserAttributeKeys where akID = ?', array($this->getAttributeKeyID()));
		$r = $db->Execute('select avID from UserAttributeValues where akID = ?', array($this->getAttributeKeyID()));
		while ($row = $r->FetchRow()) {
			$db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
		}
		$db->Execute('delete from UserAttributeValues where akID = ?', array($this->getAttributeKeyID()));
	}

	public static function getColumnHeaderList() {
		return parent::getList('user', array('akIsColumnHeader' => 1));	
	}
	public static function getSearchableList() {
		return parent::getList('user', array('akIsSearchable' => 1));	
	}

	public static function getImporterList() {
		return parent::getList('user', array('akIsAutoCreated' => 1));	
	}

	public static function getRegistrationList() {
		$tattribs = self::getList();
		$attribs = array();
		foreach($tattribs as $uak) {
			if (!$uak->isAttributeKeyDisplayedOnRegister()) {
				continue;
			}
			
			$attribs[] = $uak;
		}
		unset($tattribs);
		return $attribs;
	}

	public static function getUserAddedList() {
		return parent::getList('user', array('akIsAutoCreated' => 0));	
	}


	function updateAttributesDisplayOrder($uats) {
		$db = Loader::db();
		for ($i = 0; $i < count($uats); $i++) {
			$v = array($uats[$i]);
			$db->query("update UserAttributeKeys set displayOrder = {$i} where akID = ?", $v);
		}
	}


}

class UserAttributeValue extends AttributeValue {

	public function setUser($uo) {
		$this->u = $uo;
	}
	
	public static function getByID($avID) {
		$uav = new UserAttributeValue();
		$uav->load($avID);
		if ($uav->getAttributeValueID() == $avID) {
			return $uav;
		}
	}

	public function delete() {
		parent::delete();
		$db = Loader::db();
		$db->Execute('delete from UserAttributeValues where uID = ? and akID = ? and avID = ?', array(
			$this->u->getUserID(), 
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));
	}
}