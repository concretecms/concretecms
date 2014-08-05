<?php 
namespace Concrete\Core\Page\Type\Composer\Control\Type;
use Loader;
use Core;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Page\Type\Composer\Control\CorePageProperty\CorePageProperty as CorePagePropertyControl;

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
		$class = '\\Concrete\\Core\\Page\\Type\\Composer\\Control\\CorePageProperty\\' . Loader::helper('text')->camelcase($identifier) . 'CorePageProperty';
		$object = Core::make($class);
		return $object;
	}

	public function configureFromImport($node) {
		return static::getPageTypeComposerControlByIdentifier((string) $node['handle']);
	}
	
}