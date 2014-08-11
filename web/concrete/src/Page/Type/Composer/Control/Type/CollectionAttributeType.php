<?php 
namespace Concrete\Core\Page\Type\Composer\Control\Type;
use Loader;
use \Concrete\Core\Foundation\Object;
use CollectionAttributeKey;
use \Concrete\Core\Page\Type\Composer\Control\CollectionAttributeControl;
use \Concrete\Core\Attribute\Key\Key as AttributeKey;

class CollectionAttributeType extends Type {

	public function getPageTypeComposerControlObjects() {
		$objects = array();
		$keys = AttributeKey::getList('collection');

		foreach($keys as $ak) {
			$ac = new CollectionAttributeControl();
			$ac->setAttributeKeyID($ak->getAttributeKeyID());
			$ac->setPageTypeComposerControlIconSRC($ak->getAttributeKeyIconSRC());
			$ac->setPageTypeComposerControlName($ak->getAttributeKeyDisplayName());
			$objects[] = $ac;
		}
		return $objects;
	}

	public function getPageTypeComposerControlByIdentifier($identifier) {
		$ak = CollectionAttributeKey::getByID($identifier);
		$ax = new CollectionAttributeControl();
		$ax->setAttributeKeyID($ak->getAttributeKeyID());
		$ax->setPageTypeComposerControlIconSRC($ak->getAttributeKeyIconSRC($ak));
		$ax->setPageTypeComposerControlName($ak->getAttributeKeyDisplayName());
		return $ax;
	}

	public function configureFromImport($node) {
		$ak = CollectionAttributeKey::getByHandle((string) $node['handle']);
		return static::getPageTypeComposerControlByIdentifier($ak->getAttributeKeyID());
	}
	

}