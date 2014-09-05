<?php
namespace Concrete\Core\Attribute\Value;
use Loader;
class UserValue extends Value {

	/**
	 * @param UserInfo $uo
	 */
	public function setUser($uo) {
		$this->u = $uo;
	}

	/**
	 * @return UserInfo
	 */
	public function getUser() {
		return $this->u;
	}

	public static function getByID($avID) {
		$uav = new static();
		$uav->load($avID);
		if ($uav->getAttributeValueID() == $avID) {
			return $uav;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from UserAttributeValues where uID = ? and akID = ? and avID = ?', array(
			$this->u->getUserID(),
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));
		// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
		$num = $db->GetOne('select count(avID) from UserAttributeValues where avID = ?', array($this->getAttributeValueID()));
		if ($num < 1) {
			parent::delete();
		}

	}
}
