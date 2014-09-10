<?php
namespace Concrete\Core\Attribute\Key;
use \Concrete\Core\Attribute\Type as AttributeType;
use Loader;
use CacheLocal;
use Package;
use \Concrete\Core\Attribute\Value\ValueList as AttributeValueList;
use \Concrete\Core\Attribute\Value\UserValue as UserAttributeValue;

class UserKey extends Key {

	public function getIndexedSearchTable() {
		return 'UserSearchIndexAttributes';
	}

	protected $searchIndexFieldDefinition = array(
		'columns' => array(
			array('name' => 'uID', 'type' => 'integer', 'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true))
		),
		'primary' => array('uID')
	);

	public static function getAttributes($uID, $method = 'getValue') {
		$db = Loader::db();
		$values = $db->GetAll("select avID, akID from UserAttributeValues where uID = ?", array($uID));
		$avl = new AttributeValueList();
		foreach($values as $val) {
			$ak = static::getByID($val['akID']);
			if (is_object($ak)) {
				$value = $ak->getAttributeValue($val['avID'], $method);
				$avl->addAttributeValue($ak, $value);
			}
		}
		return $avl;
	}

	public function getAttributeKeyDisplayOrder() {return $this->displayOrder;}


	public function load($akID) {
		parent::load($akID);
		$db = Loader::db();
		$row = $db->GetRow("select uakProfileDisplay, uakMemberListDisplay, displayOrder, uakProfileEdit, uakProfileEditRequired, uakRegisterEdit, uakRegisterEditRequired, uakIsActive from UserAttributeKeys where akID = ?", array($akID));
		$this->setPropertiesFromArray($row);
	}

	public function getAttributeValue($avID, $method = 'getValue') {
		$av = UserAttributeValue::getByID($avID);
		$av->setAttributeKey($this);
		return $av->{$method}();
	}

	public static function getByID($akID) {
		$ak = new static();
		$ak->load($akID);
		if ($ak->getAttributeKeyID() > 0) {
			return $ak;
		}
	}

	public static function getByHandle($akHandle) {
		$ak = CacheLocal::getEntry('user_attribute_key_by_handle', $akHandle);
		if (is_object($ak)) {
			return $ak;
		} else if ($ak == -1) {
			return false;
		}
		$ak = -1;
		$db = Loader::db();

		$q = "SELECT ak.akID
			FROM AttributeKeys ak
			INNER JOIN AttributeKeyCategories akc ON ak.akCategoryID = akc.akCategoryID
			WHERE ak.akHandle = ?
			AND akc.akCategoryHandle = 'user'";
		$akID = $db->GetOne($q, array($akHandle));
		if ($akID > 0) {
			$ak = static::getByID($akID);
		}

		CacheLocal::set('user_attribute_key_by_handle', $akHandle, $ak);
		if ($ak === -1) {
			return false;
		}
		return $ak;
	}

	public function export($axml) {
		$akey = parent::export($axml);
		$akey->addAttribute('profile-displayed', $this->uakProfileDisplay);
		$akey->addAttribute('profile-editable', $this->uakProfileEdit);
		$akey->addAttribute('profile-required',$this->uakProfileEditRequired);
		$akey->addAttribute('register-editable', $this->uakRegisterEdit);
		$akey->addAttribute('register-required', $this->uakRegisterEditRequired);
		$akey->addAttribute('member-list-displayed', $this->uakMemberListDisplay);
		return $akey;
	}

	public static function import(\SimpleXMLElement $ak) {
		$type = AttributeType::getByHandle($ak['type']);
		$pkg = false;
		if ($ak['package']) {
			$pkg = Package::getByHandle($ak['package']);
		}
		$akn = static::add($type, array(
			'akHandle' => $ak['handle'],
			'akName' => $ak['name'],
			'akIsSearchableIndexed' => $ak['indexed'],
			'akIsSearchable' => $ak['searchable'],
			'uakProfileDisplay' => $ak['profile-displayed'],
			'uakProfileEdit' => $ak['profile-editable'],
			'uakProfileEditRequired' => $ak['profile-required'],
			'uakRegisterEdit' => $ak['register-editable'],
			'uakRegisterEditRequired' => $ak['register-required'],
			'uakMemberListDisplay' => $ak['member-list-displayed']
		), $pkg);

		$akn->getController()->importKey($ak);

	}

	public function isAttributeKeyDisplayedOnProfile() {
		return $this->uakProfileDisplay;
	}
	public function isAttributeKeyEditableOnProfile() {
		return $this->uakProfileEdit;
	}
	public function isAttributeKeyRequiredOnProfile() {
		return $this->uakProfileEditRequired;
	}
	public function isAttributeKeyEditableOnRegister() {
		return $this->uakRegisterEdit;
	}
	public function isAttributeKeyRequiredOnRegister() {
		return $this->uakRegisterEditRequired;
	}

	public function isAttributeKeyDisplayedOnMemberList() {
		return $this->uakMemberListDisplay;
	}

	public function isAttributeKeyActive() {
		return $this->uakIsActive;
	}

	public function activate() {
		$db = Loader::db();
		$this->refreshCache();
		$db->Execute('update UserAttributeKeys set uakIsActive = 1 where akID = ?', array($this->akID));
	}

	public function deactivate() {
		$db = Loader::db();
		$this->refreshCache();
		$db->Execute('update UserAttributeKeys set uakIsActive = 0 where akID = ?', array($this->akID));
	}

	public static function getList() {
		$list = parent::getList('user');
		usort($list, function($a, $b) {
			if ($a->getAttributeKeyDisplayOrder() == $b->getAttributeKeyDisplayOrder()) {
				return 0;
			} else {
				return ($a->getAttributeKeyDisplayOrder() < $b->getAttributeKeyDisplayOrder()) ? -1 : 1;
			}
		});
		return $list;
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
		), array('uID', 'akID'));

		$uo->reindex();
		unset($uo);
	}

	public static function add($type, $args, $pkg = false) {

		CacheLocal::delete('user_attribute_key_by_handle', $args['akHandle']);

		$ak = parent::add('user', $type, $args, $pkg);

		extract($args);

		if ($uakProfileDisplay != 1) {
			$uakProfileDisplay = 0;
		}
		if ($uakMemberListDisplay != 1) {
			$uakMemberListDisplay = 0;
		}
		if ($uakProfileEdit != 1) {
			$uakProfileEdit = 0;
		}
		if ($uakProfileEditRequired != 1) {
			$uakProfileEditRequired = 0;
		}
		if ($uakRegisterEdit != 1) {
			$uakRegisterEdit = 0;
		}
		if ($uakRegisterEditRequired != 1) {
			$uakRegisterEditRequired = 0;
		}

		if (isset($uakIsActive) && (!$uakIsActive)) {
			$uakIsActive = 0;
		} else {
			$uakIsActive = 1;
		}

		$db = Loader::db();
		$displayOrder = $db->GetOne('select max(displayOrder) from UserAttributeKeys');
		if (!$displayOrder) {
			$displayOrder = 0;
		}
		$displayOrder++;
		$v = array($ak->getAttributeKeyID(), $uakProfileDisplay, $uakMemberListDisplay, $uakProfileEdit, $uakProfileEditRequired, $uakRegisterEdit, $uakRegisterEditRequired, $displayOrder, $uakIsActive);
		$db->Execute('insert into UserAttributeKeys (akID, uakProfileDisplay, uakMemberListDisplay, uakProfileEdit, uakProfileEditRequired, uakRegisterEdit, uakRegisterEditRequired, displayOrder, uakIsActive) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', $v);

		$nak = new static();
		$nak->load($ak->getAttributeKeyID());
		return $nak;
	}

	public function update($args) {
		$ak = parent::update($args);

		extract($args);

		if ($uakProfileDisplay != 1) {
			$uakProfileDisplay = 0;
		}
		if ($uakMemberListDisplay != 1) {
			$uakMemberListDisplay = 0;
		}
		if ($uakProfileEdit != 1) {
			$uakProfileEdit = 0;
		}
		if ($uakProfileEditRequired != 1) {
			$uakProfileEditRequired = 0;
		}
		if ($uakRegisterEdit != 1) {
			$uakRegisterEdit = 0;
		}
		if ($uakRegisterEditRequired != 1) {
			$uakRegisterEditRequired = 0;
		}
		$db = Loader::db();
		$v = array($uakProfileDisplay, $uakMemberListDisplay, $uakProfileEdit, $uakProfileEditRequired, $uakRegisterEdit, $uakRegisterEditRequired, $ak->getAttributeKeyID());
		$db->Execute('update UserAttributeKeys set uakProfileDisplay = ?, uakMemberListDisplay = ?, uakProfileEdit= ?, uakProfileEditRequired = ?, uakRegisterEdit = ?, uakRegisterEditRequired = ? where akID = ?', $v);
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
	public static function getEditableList() {
		return parent::getList('user', array('akIsEditable' => 1));
	}
	public static function getSearchableList() {
		return parent::getList('user', array('akIsSearchable' => 1));
	}
	public static function getSearchableIndexedList() {
		return parent::getList('user', array('akIsSearchableIndexed' => 1));
	}
	public static function getImporterList() {
		return parent::getList('user', array('akIsAutoCreated' => 1));
	}

	public static function getPublicProfileList() {
		$tattribs = self::getList();
		$attribs = array();
		foreach($tattribs as $uak) {
			if ((!$uak->isAttributeKeyDisplayedOnProfile()) || (!$uak->isAttributeKeyActive())) {
				continue;
			}
			$attribs[] = $uak;
		}
		unset($tattribs);
		return $attribs;
	}

	public static function getRegistrationList() {
		$tattribs = self::getList();
		$attribs = array();
		foreach($tattribs as $uak) {
			if ((!$uak->isAttributeKeyEditableOnRegister()) || (!$uak->isAttributeKeyActive())) {
				continue;
			}
			$attribs[] = $uak;
		}
		unset($tattribs);
		return $attribs;
	}

	public static function getMemberListList() {
		$tattribs = self::getList();
		$attribs = array();
		foreach($tattribs as $uak) {
			if ((!$uak->isAttributeKeyDisplayedOnMemberList()) || (!$uak->isAttributeKeyActive())) {
				continue;
			}
			$attribs[] = $uak;
		}
		unset($tattribs);
		return $attribs;
	}

	public static function getEditableInProfileList() {
		$tattribs = self::getList();
		$attribs = array();
		foreach($tattribs as $uak) {
			if ((!$uak->isAttributeKeyEditableOnProfile()) || (!$uak->isAttributeKeyActive())) {
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


	public static function updateAttributesDisplayOrder($uats) {
		$db = Loader::db();
		for ($i = 0; $i < count($uats); $i++) {
			$uak = static::getByID($uats[$i]);
			$uak->refreshCache();
			$v = array($uats[$i]);
			$db->query("update UserAttributeKeys set displayOrder = {$i} where akID = ?", $v);
		}
	}


}
