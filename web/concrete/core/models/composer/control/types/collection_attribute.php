<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CollectionAttributeComposerControlType extends ComposerControlType {

	public function getComposerControlObjects() {
		$objects = array();
		$keys = AttributeKey::getList('collection');

		foreach($keys as $ak) {
			$ac = new CollectionAttributeComposerControl();
			$ac->setAttributeKeyID($ak->getAttributeKeyID());
			$ac->setComposerControlIconSRC($ak->getAttributeKeyIconSRC());
			$ac->setComposerControlName($ak->getAttributeKeyName());
			$objects[] = $ac;
		}
		return $objects;
	}

	public function getComposerControlByIdentifier($identifier) {

	}

}