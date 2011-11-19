<?

class BlogStartingPointPackage extends StartingPointPackage {

	protected $pkgHandle = 'blog';
	
	public function getPackageName() {
		return t('Blog');
	}
	
	public function getPackageDescription() {
		return t('Creates a concrete5-powered blog, complete with composer integration, integrated date navigation and tag-based searching, and a custom blog theme.');
	}
	
}