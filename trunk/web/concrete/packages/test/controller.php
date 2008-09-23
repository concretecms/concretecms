<?

defined('C5_EXECUTE') or die(_("Access Denied."));

class TestPackage extends Package {

	protected $pkgDescription = "Test Package.";
	protected $pkgName = "Test";
	protected $pkgHandle = 'test';
	
	public function install() {
		$pkg = parent::install();
		$db = Loader::db();
		
		
	}




}

?>