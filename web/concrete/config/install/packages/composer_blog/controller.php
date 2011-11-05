<?

class ComposerBlogStartingPointPackage extends StartingPointPackage {

	protected $pkgHandle = 'composer_blog';
	
	public function getPackageName() {
		return t('Composer Blog');
	}
	
	public function getPackageDescription() {
		return t('Creates a basic blog powered by Composer.');
	}
	
}