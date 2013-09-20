<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CollectionAttributePageTypeComposerControlType extends PageTypeComposerControlType {

	public function getPageTypeComposerControlObjects() {
		$objects = array();
		$keys = AttributeKey::getList('collection');

		foreach($keys as $ak) {
			$ac = new CollectionAttributePageTypeComposerControl();
			$ac->setAttributeKeyID($ak->getAttributeKeyID());
			$ac->setPageTypeComposerControlIconSRC($ak->getAttributeKeyIconSRC());
			$ac->setPageTypeComposerControlName($ak->getAttributeKeyName());
			$objects[] = $ac;
		}
		return $objects;
	}

	public function getPageTypeComposerControlByIdentifier($identifier) {
		$ak = CollectionAttributeKey::getByID($identifier);
		$ax = new CollectionAttributePageTypeComposerControl();
		$ax->setAttributeKeyID($ak->getAttributeKeyID());
		$ax->setPageTypeComposerControlIconSRC($ak->getAttributeKeyIconSRC($ak));
		$ax->setPageTypeComposerControlName($ak->getAttributeKeyName());
		return $ax;
	}

	public function configureFromImport($node) {
		$ak = CollectionAttributeKey::getByHandle((string) $node['handle']);
		return CollectionAttributePageTypeComposerControlType::getPageTypeComposerControlByIdentifier($ak->getAttributeKeyID());
	}
	

}