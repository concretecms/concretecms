<?
use \Concrete\Core\Package\StartingPointPackage;
class BootstrapStartingPointPackage extends StartingPointPackage {

	protected $pkgHandle = 'bootstrap';
	
	public function getPackageName() {
		return t('Empty Site');
	}
	
	public function getPackageDescription() {
		return t('Creates an empty site using a basic plain Bootstrap theme.');
	}
	
}