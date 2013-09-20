<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_CorePagePropertyPageTypeComposerControlType extends PageTypeComposerControlType {

	protected $corePageProperties = array(
		'name', 'url_slug', 'date_time', 'description', 'user', 'page_template', 'publish_target'
	);

	public function getPageTypeComposerControlObjects() {
		foreach($this->corePageProperties as $propertyHandle) {
			$objects[] = $this->getPageTypeComposerControlByIdentifier($propertyHandle);	
		}
		return $objects;
	}

	public function getPageTypeComposerControlByIdentifier($identifier) {
		$class = Loader::helper('text')->camelcase($identifier) . 'CorePagePropertyPageTypeComposerControl';
		$object = new $class();
		return $object;
	}

	public function configureFromImport($node) {
		return CorePagePropertyPageTypeComposerControlType::getPageTypeComposerControlByIdentifier((string) $node['handle']);
	}
	
}