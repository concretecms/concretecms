<?

class StandardStartingPointPackage extends StartingPointPackage {

	protected $pkgHandle = 'standard';
	
	public function getPackageName() {
		return t('Sample Content with Blog');
	}
	
	public function getPackageDescription() {
		return t('A great starting point for an attractive website with a blog.');
	}
	
}