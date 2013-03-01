<?

class BootstrapStartingPointPackage extends StartingPointPackage {

	protected $pkgHandle = 'bootstrap';
	
	public function getPackageName() {
		return t('Bootstrap');
	}
	
	public function getPackageDescription() {
		return t('Creates a Site using a Twitter Bootstrap Theme.');
	}
	
}