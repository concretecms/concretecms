<?

class StandardStartingPointPackage extends StartingPointPackage {

	protected $pkgHandle = 'standard';

	public function getPackageName() {
		return t('Standard Website');
	}
	
	public function getPackageDescription() {
		return t('Creates a simple website with common examples of concrete5 functionality, including the YouTube block, the slideshow block, the guestbook block, several page types and more.');
	}
	
}