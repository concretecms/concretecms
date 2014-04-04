<?php 
namespace Concrete\Core\Page\Type\Composer\Control\Type;
use Loader;
use \Concrete\Core\Foundation\Object;
class CorePagePropertyType extends Type {

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