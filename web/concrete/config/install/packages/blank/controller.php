<?

class BlankStartingPointPackage extends StartingPointPackage {

	protected $pkgHandle = 'blank';
	
	public function getPackageName() {
		return t('Empty Site');
	}
	
	public function getPackageDescription() {
		return t('Install only items required for concrete5 to run. This will create a blank site.');
	}
	
}