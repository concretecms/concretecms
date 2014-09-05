<?php
namespace Concrete\Core\Attribute\Value;
use Loader;
class FileValue extends Value {

	/**
	 * @param File $f
	 */
	public function setFile($f) {
		$this->f = $f;
	}

	/**
	 * @return File
	 */
	public function getFile() {
		return $this->f;
	}

	public static function getByID($avID) {
		$fav = new static();
		$fav->load($avID);
		if ($fav->getAttributeValueID() == $avID) {
			return $fav;
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from FileAttributeValues where fID = ? and fvID = ? and akID = ? and avID = ?', array(
			$this->f->getFileID(),
			$this->f->getFileVersionID(),
			$this->attributeKey->getAttributeKeyID(),
			$this->getAttributeValueID()
		));

		// Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
		$num = $db->GetOne('select count(avID) from FileAttributeValues where avID = ?', array($this->getAttributeValueID()));
		if ($num < 1) {
			parent::delete();
		}
	}
}
