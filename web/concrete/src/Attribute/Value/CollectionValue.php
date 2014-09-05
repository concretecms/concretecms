<?php
namespace Concrete\Core\Attribute\Value;
use Loader;
class CollectionValue extends Value {

	/**
	 * @param Collection $cObj
	 */
	public function setCollection($cObj) {
		$this->c = $cObj;
	}

	/**
	 * @return Collection
	 */
	public function getCollection() {
		return $this->c;
	}

	public static function getByID($avID) {
		$cav = new static();
		$cav->load($avID);
		if ($cav->getAttributeValueID() == $avID) {
			return $cav;
		}
	}

	public function __destruct() {
		parent::__destruct();
		unset($this->c);
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from CollectionAttributeValues where cID = ? and cvID = ? and akID = ? and avID = ?', array(
			$this->c->getCollectionID(),
			$this->c->getVersionID(),
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));

		// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
		// Note: we're going to keep these around and not delete them. We'll just clean this up with an optimize job later on - this'll speed up page deletion by a ton.

		/*
		$num = $db->GetOne('select count(avID) from CollectionAttributeValues where avID = ?', array($this->getAttributeValueID()));
		if ($num < 1) {
			parent::delete();
		}
		*/



	}
}
