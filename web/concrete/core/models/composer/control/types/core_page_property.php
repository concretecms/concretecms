<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CorePagePropertyComposerControlType extends ComposerControlType {

	protected $corePageProperties = array(
		'name', 'url_slug', 'date_time', 'description', 'user', 'page_type', 'publish_target'
	);

	public function getComposerControlObjects() {
		foreach($this->corePageProperties as $propertyHandle) {
			$objects[] = $this->getComposerControlByIdentifier($propertyHandle);	
		}
		return $objects;
	}

	public function getComposerControlByIdentifier($identifier) {
		$class = Loader::helper('text')->camelcase($identifier) . 'CorePagePropertyComposerControl';
		$object = new $class();
		return $object;
	}

	public function configureFromImport($node) {
		return CorePagePropertyComposerControlType::getComposerControlByIdentifier((string) $node['handle']);
	}
	
}