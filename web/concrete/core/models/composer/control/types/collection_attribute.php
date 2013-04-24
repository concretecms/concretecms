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
		$ak = CollectionAttributeKey::getByID($identifier);
		$ax = new CollectionAttributeComposerControl();
		$ax->setAttributeKeyID($ak->getAttributeKeyID());
		$ax->setComposerControlIconSRC($ak->getAttributeKeyIconSRC($ak));
		$ax->setComposerControlName($ak->getAttributeKeyName());
		return $ax;
	}

	public function configureFromImport($node) {
		$ak = CollectionAttributeKey::getByHandle((string) $node['handle']);
		return CollectionAttributeComposerControlType::getComposerControlByIdentifier($ak->getAttributeKeyID());
	}
	

}